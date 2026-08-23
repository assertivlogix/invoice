<?php

namespace App\Services;

use App\Models\ActivityLog;
use App\Models\CompanySetting;
use App\Models\Invoice;
use App\Models\InvoiceItem;
use App\Models\InvoiceTax;
use App\Models\PaymentLink;
use App\Models\Tax;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class InvoiceService
{
    public function generateInvoiceNumber(): string
    {
        $settings = CompanySetting::getSettings();
        $prefix = $settings->invoice_prefix ?: 'INV';
        $year = date('Y');
        
        $latest = Invoice::where('invoice_number', 'like', "{$prefix}-{$year}-%")
            ->orderBy('id', 'desc')
            ->first();

        if ($latest) {
            $parts = explode('-', $latest->invoice_number);
            $lastNum = (int) end($parts);
            $nextNum = $lastNum + 1;
        } else {
            $nextNum = (int) ($settings->invoice_starting_number ?: 1);
        }

        return sprintf("%s-%s-%04d", $prefix, $year, $nextNum);
    }

    public function calculateInvoiceTotals(array $data, array $items): array
    {
        $subtotal = 0.00;
        $processedItems = [];

        foreach ($items as $item) {
            $qty = (float) ($item['quantity'] ?? 1);
            $rate = (float) ($item['unit_price'] ?? 0);
            $itemSubtotal = $qty * $rate;

            $itemDiscType = $item['discount_type'] ?? 'fixed';
            $itemDiscVal = (float) ($item['discount_value'] ?? 0);
            $itemDiscAmount = 0.00;

            if ($itemDiscType === 'percentage') {
                $itemDiscAmount = ($itemSubtotal * $itemDiscVal) / 100;
            } else {
                $itemDiscAmount = min($itemDiscVal, $itemSubtotal);
            }

            $itemTaxable = $itemSubtotal - $itemDiscAmount;
            $itemTaxRate = (float) ($item['tax_rate'] ?? 0);
            $itemTaxAmount = ($itemTaxable * $itemTaxRate) / 100;
            $itemTotal = $itemTaxable + $itemTaxAmount;

            $subtotal += $itemSubtotal;

            $processedItems[] = array_merge($item, [
                'quantity' => $qty,
                'unit_price' => $rate,
                'discount_type' => $itemDiscType,
                'discount_value' => $itemDiscVal,
                'discount_amount' => round($itemDiscAmount, 2),
                'tax_rate' => $itemTaxRate,
                'tax_amount' => round($itemTaxAmount, 2),
                'total_amount' => round($itemTotal, 2),
            ]);
        }

        // Invoice-level discount
        $discType = $data['discount_type'] ?? 'fixed';
        $discVal = (float) ($data['discount_value'] ?? 0);
        $discAmount = 0.00;

        if ($discType === 'percentage') {
            $discAmount = ($subtotal * $discVal) / 100;
        } else {
            $discAmount = min($discVal, $subtotal);
        }

        $taxableAmount = max(0, $subtotal - $discAmount);

        // Taxes
        $totalTaxAmount = 0.00;
        $taxesBreakdown = [];

        if (!empty($data['selected_taxes']) && is_array($data['selected_taxes'])) {
            $taxes = Tax::whereIn('id', $data['selected_taxes'])->get();
            foreach ($taxes as $t) {
                $tAmount = ($taxableAmount * (float)$t->rate) / 100;
                $totalTaxAmount += $tAmount;
                $taxesBreakdown[] = [
                    'tax_id' => $t->id,
                    'tax_name' => $t->name,
                    'tax_rate' => $t->rate,
                    'tax_amount' => round($tAmount, 2),
                ];
            }
        }

        $addCharges = (float) ($data['additional_charges'] ?? 0);
        $rawGrand = $taxableAmount + $totalTaxAmount + $addCharges;
        $roundOff = (float) ($data['round_off'] ?? 0);
        $grandTotal = max(0, $rawGrand + $roundOff);

        return [
            'subtotal' => round($subtotal, 2),
            'discount_type' => $discType,
            'discount_value' => $discVal,
            'discount_amount' => round($discAmount, 2),
            'taxable_amount' => round($taxableAmount, 2),
            'tax_amount' => round($totalTaxAmount, 2),
            'additional_charges' => round($addCharges, 2),
            'round_off' => round($roundOff, 2),
            'grand_total' => round($grandTotal, 2),
            'items' => $processedItems,
            'taxes_breakdown' => $taxesBreakdown,
        ];
    }

    public function createInvoice(array $data, array $items): Invoice
    {
        return DB::transaction(function () use ($data, $items) {
            $calculated = $this->calculateInvoiceTotals($data, $items);
            
            $invoiceNumber = !empty($data['invoice_number']) 
                ? $data['invoice_number'] 
                : $this->generateInvoiceNumber();

            $paidAmount = (float) ($data['paid_amount'] ?? 0);
            $grandTotal = $calculated['grand_total'];
            $balanceDue = max(0, $grandTotal - $paidAmount);

            $status = $data['status'] ?? 'Draft';
            if ($status !== 'Cancelled') {
                if ($balanceDue == 0 && $grandTotal > 0) {
                    $status = 'Paid';
                } elseif ($paidAmount > 0 && $balanceDue > 0) {
                    $status = 'Partially Paid';
                }
            }

            $invoice = Invoice::create([
                'invoice_number' => $invoiceNumber,
                'client_id' => $data['client_id'],
                'project_id' => $data['project_id'] ?? null,
                'invoice_date' => $data['invoice_date'],
                'due_date' => $data['due_date'],
                'currency' => $data['currency'] ?? 'USD',
                'payment_terms' => $data['payment_terms'] ?? 'Net 30',
                'reference_number' => $data['reference_number'] ?? null,
                'po_number' => $data['po_number'] ?? null,
                'subtotal' => $calculated['subtotal'],
                'discount_type' => $calculated['discount_type'],
                'discount_value' => $calculated['discount_value'],
                'discount_amount' => $calculated['discount_amount'],
                'taxable_amount' => $calculated['taxable_amount'],
                'tax_amount' => $calculated['tax_amount'],
                'additional_charges' => $calculated['additional_charges'],
                'round_off' => $calculated['round_off'],
                'grand_total' => $calculated['grand_total'],
                'paid_amount' => round($paidAmount, 2),
                'balance_due' => round($balanceDue, 2),
                'status' => $status,
                'template' => $data['template'] ?? 'modern',
                'notes' => $data['notes'] ?? null,
                'terms_conditions' => $data['terms_conditions'] ?? null,
                'created_by' => auth()->id(),
            ]);

            // Save items
            foreach ($calculated['items'] as $item) {
                InvoiceItem::create([
                    'invoice_id' => $invoice->id,
                    'service_id' => $item['service_id'] ?? null,
                    'item_name' => $item['item_name'],
                    'description' => $item['description'] ?? null,
                    'quantity' => $item['quantity'],
                    'unit' => $item['unit'] ?? 'Item',
                    'unit_price' => $item['unit_price'],
                    'discount_type' => $item['discount_type'],
                    'discount_value' => $item['discount_value'],
                    'discount_amount' => $item['discount_amount'],
                    'tax_rate' => $item['tax_rate'],
                    'tax_amount' => $item['tax_amount'],
                    'total_amount' => $item['total_amount'],
                ]);
            }

            // Save tax breakdown
            foreach ($calculated['taxes_breakdown'] as $tb) {
                InvoiceTax::create([
                    'invoice_id' => $invoice->id,
                    'tax_id' => $tb['tax_id'],
                    'tax_name' => $tb['tax_name'],
                    'tax_rate' => $tb['tax_rate'],
                    'tax_amount' => $tb['tax_amount'],
                ]);
            }

            // Generate payment link if active/sent and balance due > 0
            if ($balanceDue > 0) {
                $this->createPaymentLink($invoice);
            }

            ActivityLog::log('created', "Invoice {$invoice->invoice_number} created for total {$invoice->currency_symbol}{$invoice->grand_total}", $invoice);

            return $invoice;
        });
    }

    public function updateInvoice(Invoice $invoice, array $data, array $items): Invoice
    {
        return DB::transaction(function () use ($invoice, $data, $items) {
            $calculated = $this->calculateInvoiceTotals($data, $items);
            
            $grandTotal = $calculated['grand_total'];
            $paidAmount = $invoice->paid_amount;
            $balanceDue = max(0, $grandTotal - $paidAmount);

            $status = $data['status'] ?? $invoice->status;
            if ($status !== 'Cancelled') {
                if ($balanceDue == 0 && $grandTotal > 0) {
                    $status = 'Paid';
                } elseif ($paidAmount > 0 && $balanceDue > 0) {
                    $status = 'Partially Paid';
                }
            }

            $invoice->update([
                'client_id' => $data['client_id'],
                'project_id' => $data['project_id'] ?? null,
                'invoice_date' => $data['invoice_date'],
                'due_date' => $data['due_date'],
                'currency' => $data['currency'] ?? $invoice->currency,
                'payment_terms' => $data['payment_terms'] ?? $invoice->payment_terms,
                'reference_number' => $data['reference_number'] ?? null,
                'po_number' => $data['po_number'] ?? null,
                'subtotal' => $calculated['subtotal'],
                'discount_type' => $calculated['discount_type'],
                'discount_value' => $calculated['discount_value'],
                'discount_amount' => $calculated['discount_amount'],
                'taxable_amount' => $calculated['taxable_amount'],
                'tax_amount' => $calculated['tax_amount'],
                'additional_charges' => $calculated['additional_charges'],
                'round_off' => $calculated['round_off'],
                'grand_total' => $calculated['grand_total'],
                'balance_due' => round($balanceDue, 2),
                'status' => $status,
                'template' => $data['template'] ?? $invoice->template,
                'notes' => $data['notes'] ?? null,
                'terms_conditions' => $data['terms_conditions'] ?? null,
            ]);

            // Replace items
            $invoice->items()->delete();
            foreach ($calculated['items'] as $item) {
                InvoiceItem::create([
                    'invoice_id' => $invoice->id,
                    'service_id' => $item['service_id'] ?? null,
                    'item_name' => $item['item_name'],
                    'description' => $item['description'] ?? null,
                    'quantity' => $item['quantity'],
                    'unit' => $item['unit'] ?? 'Item',
                    'unit_price' => $item['unit_price'],
                    'discount_type' => $item['discount_type'],
                    'discount_value' => $item['discount_value'],
                    'discount_amount' => $item['discount_amount'],
                    'tax_rate' => $item['tax_rate'],
                    'tax_amount' => $item['tax_amount'],
                    'total_amount' => $item['total_amount'],
                ]);
            }

            // Replace tax breakdown
            $invoice->taxes()->delete();
            foreach ($calculated['taxes_breakdown'] as $tb) {
                InvoiceTax::create([
                    'invoice_id' => $invoice->id,
                    'tax_id' => $tb['tax_id'],
                    'tax_name' => $tb['tax_name'],
                    'tax_rate' => $tb['tax_rate'],
                    'tax_amount' => $tb['tax_amount'],
                ]);
            }

            // Update or generate payment link
            if ($balanceDue > 0) {
                $this->createPaymentLink($invoice);
            }

            ActivityLog::log('updated', "Invoice {$invoice->invoice_number} updated", $invoice);

            return $invoice;
        });
    }

    public function duplicateInvoice(Invoice $invoice): Invoice
    {
        return DB::transaction(function () use ($invoice) {
            $newNumber = $this->generateInvoiceNumber();
            $newInvoice = $invoice->replicate(['invoice_number', 'paid_amount', 'balance_due', 'status', 'cancelled_at', 'cancelled_by', 'cancellation_reason']);
            
            $newInvoice->invoice_number = $newNumber;
            $newInvoice->invoice_date = now()->format('Y-m-d');
            $newInvoice->due_date = now()->addDays(30)->format('Y-m-d');
            $newInvoice->status = 'Draft';
            $newInvoice->paid_amount = 0.00;
            $newInvoice->balance_due = $invoice->grand_total;
            $newInvoice->created_by = auth()->id();
            $newInvoice->save();

            foreach ($invoice->items as $item) {
                $newItem = $item->replicate();
                $newItem->invoice_id = $newInvoice->id;
                $newItem->save();
            }

            foreach ($invoice->taxes as $t) {
                $newTax = $t->replicate();
                $newTax->invoice_id = $newInvoice->id;
                $newTax->save();
            }

            ActivityLog::log('created', "Invoice {$invoice->invoice_number} duplicated as {$newNumber}", $newInvoice);

            return $newInvoice;
        });
    }

    public function cancelInvoice(Invoice $invoice, string $reason): Invoice
    {
        $invoice->update([
            'status' => 'Cancelled',
            'cancelled_at' => now(),
            'cancelled_by' => auth()->id(),
            'cancellation_reason' => $reason,
        ]);

        // Cancel active payment links
        $invoice->paymentLinks()->where('status', 'Active')->update(['status' => 'Cancelled']);

        ActivityLog::log('cancelled', "Invoice {$invoice->invoice_number} cancelled. Reason: {$reason}", $invoice);

        return $invoice;
    }

    public function createPaymentLink(Invoice $invoice): PaymentLink
    {
        // Cancel existing active links for this invoice
        $invoice->paymentLinks()->where('status', 'Active')->update(['status' => 'Cancelled']);

        $token = Str::random(40);

        return PaymentLink::create([
            'invoice_id' => $invoice->id,
            'client_id' => $invoice->client_id,
            'token' => $token,
            'gateway' => 'Stripe',
            'amount' => $invoice->balance_due,
            'currency' => $invoice->currency,
            'expires_at' => now()->addDays(60),
            'status' => 'Active',
        ]);
    }

    public function updateOverdueStatuses(): int
    {
        $updated = Invoice::whereNotIn('status', ['Paid', 'Cancelled', 'Overdue'])
            ->where('due_date', '<', now()->format('Y-m-d'))
            ->where('balance_due', '>', 0)
            ->update(['status' => 'Overdue']);

        return $updated;
    }
}
