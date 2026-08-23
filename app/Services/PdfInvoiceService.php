<?php

namespace App\Services;

use App\Models\CompanySetting;
use App\Models\Invoice;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Storage;

class PdfInvoiceService
{
    public function generatePdf(Invoice $invoice, ?string $template = null): string
    {
        $company = CompanySetting::getSettings();
        $invoice->load(['client', 'project', 'items', 'taxes', 'payments']);
        
        $tpl = $template ?: ($invoice->template ?: 'modern');
        $viewName = "pdf.invoice_{$tpl}";

        if (!view()->exists($viewName)) {
            $viewName = 'pdf.invoice_modern';
        }

        $pdf = Pdf::loadView($viewName, [
            'invoice' => $invoice,
            'company' => $company,
            'isPaid' => $invoice->status === 'Paid',
        ]);

        $pdf->setPaper('A4', 'portrait');

        $fileName = "{$invoice->invoice_number}.pdf";
        if ($invoice->status === 'Paid') {
            $fileName = "{$invoice->invoice_number}-PAID.pdf";
        }

        $path = "invoices/{$fileName}";
        Storage::disk('public')->put($path, $pdf->output());

        return storage_path("app/public/{$path}");
    }

    public function generatePaidPdf(Invoice $invoice): string
    {
        return $this->generatePdf($invoice);
    }
}
