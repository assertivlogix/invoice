<?php

namespace App\Services;

use App\Models\EmailLog;
use App\Models\Invoice;
use App\Models\Payment;
use Illuminate\Support\Facades\Mail;

class EmailInvoiceService
{
    public function sendInvoiceEmail(Invoice $invoice, ?string $pdfPath = null): bool
    {
        $client = $invoice->client;
        $recipient = $client->email;
        $subject = "Invoice {$invoice->invoice_number} from Assertiv Logix";

        try {
            Mail::send('emails.invoice', [
                'invoice' => $invoice,
                'client' => $client,
                'paymentLink' => $invoice->activePaymentLink ? url("/pay/invoice/{$invoice->activePaymentLink->token}") : null,
            ], function ($message) use ($recipient, $subject, $pdfPath) {
                $message->to($recipient)->subject($subject);
                if ($pdfPath && file_exists($pdfPath)) {
                    $message->attach($pdfPath);
                }
            });

            EmailLog::create([
                'client_id' => $invoice->client_id,
                'invoice_id' => $invoice->id,
                'recipient_email' => $recipient,
                'subject' => $subject,
                'email_type' => 'Invoice',
                'status' => 'Sent',
                'sent_at' => now(),
            ]);

            if ($invoice->status === 'Draft') {
                $invoice->update(['status' => 'Sent']);
            }

            return true;
        } catch (\Exception $e) {
            EmailLog::create([
                'client_id' => $invoice->client_id,
                'invoice_id' => $invoice->id,
                'recipient_email' => $recipient,
                'subject' => $subject,
                'email_type' => 'Invoice',
                'status' => 'Failed',
                'sent_at' => now(),
                'error_message' => $e->getMessage(),
            ]);
            return false;
        }
    }

    public function sendPaidInvoiceEmail(Invoice $invoice, Payment $payment, string $paidPdfPath): bool
    {
        $client = $invoice->client;
        $recipient = $client->email;
        $subject = "Payment Received — Invoice {$invoice->invoice_number} Paid";

        try {
            Mail::send('emails.paid_invoice', [
                'invoice' => $invoice,
                'payment' => $payment,
                'client' => $client,
            ], function ($message) use ($recipient, $subject, $paidPdfPath) {
                $message->to($recipient)->subject($subject);
                if (file_exists($paidPdfPath)) {
                    $message->attach($paidPdfPath);
                }
            });

            EmailLog::create([
                'client_id' => $invoice->client_id,
                'invoice_id' => $invoice->id,
                'payment_id' => $payment->id,
                'recipient_email' => $recipient,
                'subject' => $subject,
                'email_type' => 'Paid Invoice',
                'status' => 'Sent',
                'sent_at' => now(),
            ]);

            return true;
        } catch (\Exception $e) {
            EmailLog::create([
                'client_id' => $invoice->client_id,
                'invoice_id' => $invoice->id,
                'payment_id' => $payment->id,
                'recipient_email' => $recipient,
                'subject' => $subject,
                'email_type' => 'Paid Invoice',
                'status' => 'Failed',
                'sent_at' => now(),
                'error_message' => $e->getMessage(),
            ]);
            return false;
        }
    }

    public function sendReminderEmail(Invoice $invoice): bool
    {
        $client = $invoice->client;
        $recipient = $client->email;
        $isOverdue = $invoice->status === 'Overdue';
        $subject = $isOverdue 
            ? "Payment Reminder — Invoice {$invoice->invoice_number} Overdue"
            : "Payment Reminder — Invoice {$invoice->invoice_number} Due Soon";

        try {
            Mail::send('emails.reminder', [
                'invoice' => $invoice,
                'client' => $client,
                'isOverdue' => $isOverdue,
                'paymentLink' => $invoice->activePaymentLink ? url("/pay/invoice/{$invoice->activePaymentLink->token}") : null,
            ], function ($message) use ($recipient, $subject) {
                $message->to($recipient)->subject($subject);
            });

            EmailLog::create([
                'client_id' => $invoice->client_id,
                'invoice_id' => $invoice->id,
                'recipient_email' => $recipient,
                'subject' => $subject,
                'email_type' => $isOverdue ? 'Overdue Reminder' : 'Payment Reminder',
                'status' => 'Sent',
                'sent_at' => now(),
            ]);

            return true;
        } catch (\Exception $e) {
            EmailLog::create([
                'client_id' => $invoice->client_id,
                'invoice_id' => $invoice->id,
                'recipient_email' => $recipient,
                'subject' => $subject,
                'email_type' => $isOverdue ? 'Overdue Reminder' : 'Payment Reminder',
                'status' => 'Failed',
                'sent_at' => now(),
                'error_message' => $e->getMessage(),
            ]);
            return false;
        }
    }
}
