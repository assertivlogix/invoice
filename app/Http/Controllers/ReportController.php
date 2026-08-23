<?php

namespace App\Http\Controllers;

use App\Models\Client;
use App\Models\Invoice;
use App\Models\InvoiceTax;
use App\Models\Payment;
use Illuminate\Http\Request;

class ReportController extends Controller
{
    public function index()
    {
        return view('reports.index');
    }

    public function invoices(Request $request)
    {
        $query = Invoice::with('client');

        if ($request->filled('date_from')) {
            $query->where('invoice_date', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->where('invoice_date', '<=', $request->date_to);
        }
        if ($request->filled('client_id')) {
            $query->where('client_id', $request->client_id);
        }
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->get('export') === 'csv') {
            $invoices = $query->get();
            $csvHeader = ['Invoice Number', 'Client', 'Date', 'Due Date', 'Total', 'Paid', 'Balance', 'Status'];
            $csvData = [];

            foreach ($invoices as $inv) {
                $csvData[] = [
                    $inv->invoice_number,
                    $inv->client->company_name,
                    $inv->invoice_date->format('Y-m-d'),
                    $inv->due_date->format('Y-m-d'),
                    $inv->grand_total,
                    $inv->paid_amount,
                    $inv->balance_due,
                    $inv->status,
                ];
            }

            return $this->downloadCsv('invoice_report_' . date('Y-m-d') . '.csv', $csvHeader, $csvData);
        }

        $invoices = $query->paginate(20)->withQueryString();
        $clients = Client::orderBy('company_name')->get();

        $summary = [
            'count' => $query->count(),
            'total' => $query->sum('grand_total'),
            'paid' => $query->sum('paid_amount'),
            'balance' => $query->sum('balance_due'),
        ];

        return view('reports.invoices', compact('invoices', 'clients', 'summary'));
    }

    public function payments(Request $request)
    {
        $query = Payment::with(['client', 'invoice']);

        if ($request->filled('date_from')) {
            $query->where('payment_date', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->where('payment_date', '<=', $request->date_to);
        }
        if ($request->filled('client_id')) {
            $query->where('client_id', $request->client_id);
        }
        if ($request->filled('payment_method')) {
            $query->where('payment_method', $request->payment_method);
        }

        if ($request->get('export') === 'csv') {
            $payments = $query->get();
            $csvHeader = ['Payment ID', 'Client', 'Invoice Number', 'Payment Date', 'Amount', 'Payment Method', 'Transaction ID'];
            $csvData = [];

            foreach ($payments as $p) {
                $csvData[] = [
                    $p->payment_id,
                    $p->client->company_name,
                    $p->invoice ? $p->invoice->invoice_number : 'N/A',
                    $p->payment_date->format('Y-m-d'),
                    $p->amount,
                    $p->payment_method,
                    $p->transaction_id ?? '',
                ];
            }

            return $this->downloadCsv('payment_report_' . date('Y-m-d') . '.csv', $csvHeader, $csvData);
        }

        $payments = $query->paginate(20)->withQueryString();
        $clients = Client::orderBy('company_name')->get();

        $totalAmount = $query->sum('amount');
        $methodBreakdown = Payment::selectRaw('payment_method, COUNT(*) as count, SUM(amount) as total')
            ->groupBy('payment_method')
            ->get();

        return view('reports.payments', compact('payments', 'clients', 'totalAmount', 'methodBreakdown'));
    }

    public function revenue(Request $request)
    {
        $clients = Client::withCount('invoices')->get()->map(function ($client) {
            return [
                'client_id' => $client->client_id,
                'company_name' => $client->company_name,
                'total_invoices' => $client->invoices_count,
                'total_revenue' => $client->total_invoiced,
                'paid' => $client->total_paid,
                'outstanding' => $client->total_outstanding,
            ];
        });

        if ($request->get('export') === 'csv') {
            $csvHeader = ['Client ID', 'Client Name', 'Total Invoices', 'Total Revenue', 'Paid Amount', 'Outstanding Balance'];
            $csvData = [];

            foreach ($clients as $c) {
                $csvData[] = [
                    $c['client_id'],
                    $c['company_name'],
                    $c['total_invoices'],
                    $c['total_revenue'],
                    $c['paid'],
                    $c['outstanding'],
                ];
            }

            return $this->downloadCsv('client_revenue_report_' . date('Y-m-d') . '.csv', $csvHeader, $csvData);
        }

        return view('reports.revenue', compact('clients'));
    }

    public function tax(Request $request)
    {
        $taxRecords = InvoiceTax::with(['invoice.client'])
            ->latest('id')
            ->get();

        if ($request->get('export') === 'csv') {
            $csvHeader = ['Tax Name', 'Rate (%)', 'Tax Amount', 'Invoice Number', 'Client'];
            $csvData = [];

            foreach ($taxRecords as $tr) {
                $csvData[] = [
                    $tr->tax_name,
                    $tr->tax_rate,
                    $tr->tax_amount,
                    $tr->invoice ? $tr->invoice->invoice_number : '',
                    $tr->invoice && $tr->invoice->client ? $tr->invoice->client->company_name : '',
                ];
            }

            return $this->downloadCsv('tax_report_' . date('Y-m-d') . '.csv', $csvHeader, $csvData);
        }

        $taxSummary = InvoiceTax::selectRaw('tax_name, tax_rate, SUM(tax_amount) as total_tax')
            ->groupBy('tax_name', 'tax_rate')
            ->get();

        return view('reports.tax', compact('taxRecords', 'taxSummary'));
    }

    protected function downloadCsv(string $filename, array $header, array $rows)
    {
        $callback = function () use ($header, $rows) {
            $file = fopen('php://output', 'w');
            fputcsv($file, $header);
            foreach ($rows as $row) {
                fputcsv($file, $row);
            }
            fclose($file);
        };

        return response()->stream($callback, 200, [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ]);
    }
}
