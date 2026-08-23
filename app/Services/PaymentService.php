<?php

namespace App\Services;

use App\Models\ActivityLog;
use App\Models\Invoice;
use App\Models\Payment;
use Illuminate\Support\Facades\DB;

class PaymentService
{
    protected PdfInvoiceService $pdfService;
    protected EmailInvoiceService $emailService;

    public function __construct(PdfInvoiceService $pdfService, EmailInvoiceService $emailService)
    {
        $this->pdfService = $pdfService;
        $this->emailService = $emailService;
    }

    public function generatePaymentId(): string
    {
        $year = date('Y');
        $latest = Payment::where('payment_id', 'like', "PAY-{$year}-%")
            ->orderBy('id', 'desc')
            ->first();

        if ($latest) {
            $parts = explode('-', $latest->payment_id);
            $lastNum = (int) end($parts);
            $nextNum = $lastNum + 1;
        } else {
            $nextNum = 1;
        }

        return sprintf("PAY-%s-%05d", $year, $nextNum);
    }

    public function recordPayment(array $data): Payment
    {
        return DB::transaction(function () use ($data) {
            $invoice = Invoice::findOrFail($data['invoice_id']);
            $amount = (float) $data['amount'];

            // Cap payment at balance due unless overpayment allowed
            if ($amount > $invoice->balance_due && $invoice->balance_due > 0) {
                $amount = $invoice->balance_due;
            }

            $paymentId = $data['payment_id'] ?? $this->generatePaymentId();

            $payment = Payment::create([
                'payment_id' => $paymentId,
                'invoice_id' => $invoice->id,
                'client_id' => $invoice->client_id,
                'payment_date' => $data['payment_date'] ?? now()->format('Y-m-d'),
                'amount' => round($amount, 2),
                'payment_method' => $data['payment_method'] ?? 'Bank Transfer',
                'transaction_id' => $data['transaction_id'] ?? null,
                'reference_number' => $data['reference_number'] ?? null,
                'notes' => $data['notes'] ?? null,
                'created_by' => auth()->id(),
                'status' => 'Completed',
            ]);

            // Update invoice paid & balance due
            $newPaid = $invoice->paid_amount + $amount;
            $newBalance = max(0, $invoice->grand_total - $newPaid);

            $status = $invoice->status;
            if ($newBalance == 0 && $invoice->grand_total > 0) {
                $status = 'Paid';
            } elseif ($newPaid > 0 && $newBalance > 0) {
                $status = 'Partially Paid';
            }

            $invoice->update([
                'paid_amount' => round($newPaid, 2),
                'balance_due' => round($newBalance, 2),
                'status' => $status,
            ]);

            ActivityLog::log('paid', "Payment {$payment->payment_id} of {$invoice->currency_symbol}{$payment->amount} recorded for Invoice {$invoice->invoice_number}", $payment);

            // If fully paid, cancel active payment link and send paid email
            if ($status === 'Paid') {
                $invoice->paymentLinks()->where('status', 'Active')->update(['status' => 'Used']);
                
                // Generate watermarked PDF & send email asynchronously / inline
                try {
                    $pdfPath = $this->pdfService->generatePaidPdf($invoice);
                    $this->emailService->sendPaidInvoiceEmail($invoice, $payment, $pdfPath);
                } catch (\Exception $e) {
                    \Log::error("Failed sending paid invoice email/PDF: " . $e->getMessage());
                }
            } else {
                // Regenerate payment link for remaining balance
                app(InvoiceService::class)->createPaymentLink($invoice);
            }

            return $payment;
        });
    }
}
