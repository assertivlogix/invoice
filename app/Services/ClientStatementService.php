<?php

namespace App\Services;

use App\Models\Client;
use App\Models\Invoice;
use App\Models\Payment;
use Carbon\Carbon;

class ClientStatementService
{
    public function generateStatement(Client $client, string $startDate, string $endDate): array
    {
        $start = Carbon::parse($startDate)->startOfDay();
        $end = Carbon::parse($endDate)->endOfDay();

        // Calculate opening balance prior to start date
        $priorInvoices = Invoice::where('client_id', $client->id)
            ->where('status', '!=', 'Cancelled')
            ->where('invoice_date', '<', $start)
            ->sum('grand_total');

        $priorPayments = Payment::where('client_id', $client->id)
            ->where('status', 'Completed')
            ->where('payment_date', '<', $start)
            ->sum('amount');

        $openingBalance = (float) ($priorInvoices - $priorPayments);

        // Fetch Invoices within date range
        $invoices = Invoice::where('client_id', $client->id)
            ->where('status', '!=', 'Cancelled')
            ->whereBetween('invoice_date', [$start, $end])
            ->get();

        // Fetch Payments within date range
        $payments = Payment::where('client_id', $client->id)
            ->where('status', 'Completed')
            ->whereBetween('payment_date', [$start, $end])
            ->get();

        $transactions = collect();

        foreach ($invoices as $inv) {
            $transactions->push([
                'date' => $inv->invoice_date->format('Y-m-d'),
                'type' => 'Invoice',
                'reference' => $inv->invoice_number,
                'debit' => (float) $inv->grand_total,
                'credit' => 0.00,
                'description' => "Invoice {$inv->invoice_number}",
            ]);
        }

        foreach ($payments as $pay) {
            $transactions->push([
                'date' => $pay->payment_date->format('Y-m-d'),
                'type' => 'Payment',
                'reference' => $pay->payment_id,
                'debit' => 0.00,
                'credit' => (float) $pay->amount,
                'description' => "Payment {$pay->payment_id} ({$pay->payment_method})",
            ]);
        }

        // Sort chronologically
        $sortedTransactions = $transactions->sortBy('date')->values();

        // Calculate running balances
        $runningBalance = $openingBalance;
        $processedLedger = [];
        $totalDebit = 0.00;
        $totalCredit = 0.00;

        foreach ($sortedTransactions as $txn) {
            $runningBalance += ($txn['debit'] - $txn['credit']);
            $totalDebit += $txn['debit'];
            $totalCredit += $txn['credit'];

            $processedLedger[] = array_merge($txn, [
                'balance' => round($runningBalance, 2),
            ]);
        }

        $closingBalance = round($runningBalance, 2);

        return [
            'client' => $client,
            'startDate' => $startDate,
            'endDate' => $endDate,
            'openingBalance' => round($openingBalance, 2),
            'ledger' => $processedLedger,
            'totalDebit' => round($totalDebit, 2),
            'totalCredit' => round($totalCredit, 2),
            'closingBalance' => $closingBalance,
        ];
    }
}
