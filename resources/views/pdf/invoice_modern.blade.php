<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Invoice {{ $invoice->invoice_number }}</title>
    <style>
        body { font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif; font-size: 13px; color: #333333; line-height: 1.4; margin: 0; padding: 20px; }
        .header-table { width: 100%; margin-bottom: 25px; border-bottom: 2px solid #2563eb; padding-bottom: 15px; }
        .company-name { font-size: 22px; font-weight: bold; color: #2563eb; }
        .invoice-title { font-size: 24px; font-weight: bold; color: #111827; text-align: right; }
        .invoice-number { font-size: 15px; font-weight: bold; color: #2563eb; text-align: right; }
        .bill-to-table { width: 100%; margin-bottom: 25px; }
        .bill-to-box { background: #f8fafc; padding: 12px; border-radius: 6px; border: 1px solid #e2e8f0; }
        .items-table { width: 100%; border-collapse: collapse; margin-bottom: 25px; }
        .items-table th { background: #0f172a; color: #ffffff; padding: 8px 10px; font-size: 12px; text-transform: uppercase; text-align: left; }
        .items-table td { padding: 9px 10px; border-bottom: 1px solid #e2e8f0; font-size: 12px; }
        .totals-table { width: 45%; float: right; border-collapse: collapse; }
        .totals-table td { padding: 6px 10px; font-size: 13px; }
        .grand-total { font-weight: bold; font-size: 15px; color: #2563eb; border-top: 2px solid #2563eb; }
        .paid-watermark { position: absolute; top: 350px; left: 160px; font-size: 90px; font-weight: 900; color: rgba(16, 185, 129, 0.2); transform: rotate(-25deg); border: 8px solid rgba(16, 185, 129, 0.2); padding: 5px 40px; border-radius: 12px; z-index: 100; }
        .bank-box { background: #f1f5f9; padding: 10px; border-radius: 6px; font-size: 11px; width: 48%; float: left; }
        .footer { margin-top: 50px; border-top: 1px solid #e2e8f0; padding-top: 10px; text-align: center; font-size: 11px; color: #64748b; }
        .clear { clear: both; }
    </style>
</head>
<body>

@if($isPaid || $invoice->status === 'Paid')
    <div class="paid-watermark">PAID</div>
@endif

<table class="header-table">
    <tr>
        <td style="width: 35%; vertical-align: top;">
            <div class="company-name" style="font-size: 16px; font-weight: bold; color: #2563eb; margin-bottom: 2px;">{{ $company->company_name }}</div>
            <div style="font-size: 11px; color: #64748b;">
                {{ $company->address }}<br>
                {{ $company->city }}, {{ $company->state }} {{ $company->zip_code }} {{ $company->country }}<br>
                Email: {{ $company->email }} | Phone: {{ $company->phone }}<br>
                @if($company->tax_number) Tax ID: {{ $company->tax_number }} @endif
            </div>
        </td>
        <td style="width: 30%; text-align: center; vertical-align: middle;">
            <img src="{{ public_path('images/logo.svg') }}" alt="{{ $company->company_name }}" style="max-height: 55px; max-width: 170px;">
        </td>
        <td style="width: 35%; text-align: right; vertical-align: top;">
            <div class="invoice-title">INVOICE</div>
            <div class="invoice-number">{{ $invoice->invoice_number }}</div>
            <div style="font-size: 11px; color: #64748b; margin-top: 4px;">
                Date: <strong>{{ $invoice->invoice_date->format('M d, Y') }}</strong><br>
                Due Date: <strong>{{ $invoice->due_date->format('M d, Y') }}</strong><br>
                Terms: {{ $invoice->payment_terms }}
            </div>
        </td>
    </tr>
</table>

<table class="bill-to-table">
    <tr>
        <td style="width: 55%; vertical-align: top;">
            <div class="bill-to-box">
                <div style="font-size: 11px; color: #64748b; text-transform: uppercase; font-weight: bold;">Billed To:</div>
                <div style="font-size: 14px; font-weight: bold; color: #111827; margin-top: 3px;">{{ $invoice->client->company_name }}</div>
                @if($invoice->client->contact_person) <div>Attn: {{ $invoice->client->contact_person }}</div> @endif
                <div>{{ $invoice->client->formatted_address }}</div>
                <div>Email: {{ $invoice->client->email }}</div>
            </div>
        </td>
        <td style="width: 45%; text-align: right; vertical-align: top;">
            @if($invoice->project)
            <div style="padding: 10px;">
                <div style="font-size: 11px; color: #64748b; text-transform: uppercase; font-weight: bold;">Project:</div>
                <div style="font-size: 13px; font-weight: bold; color: #111827;">{{ $invoice->project->project_name }}</div>
            </div>
            @endif
        </td>
    </tr>
</table>

<table class="items-table">
    <thead>
        <tr>
            <th style="width: 50%;">Description</th>
            <th style="width: 12%; text-align: center;">Qty</th>
            <th style="width: 13%; text-align: center;">Unit</th>
            <th style="width: 12.5%; text-align: right;">Rate</th>
            <th style="width: 12.5%; text-align: right;">Amount</th>
        </tr>
    </thead>
    <tbody>
        @foreach($invoice->items as $item)
        <tr>
            <td>
                <strong>{{ $item->item_name }}</strong>
                @if($item->description) <br><span style="font-size: 11px; color: #64748b;">{{ $item->description }}</span> @endif
            </td>
            <td style="text-align: center;">{{ number_format($item->quantity, 2) }}</td>
            <td style="text-align: center;">{{ $item->unit }}</td>
            <td style="text-align: right;">{{ $invoice->currency_symbol }}{{ number_format($item->unit_price, 2) }}</td>
            <td style="text-align: right; font-weight: bold;">{{ $invoice->currency_symbol }}{{ number_format($item->total_amount, 2) }}</td>
        </tr>
        @endforeach
    </tbody>
</table>

<div>
    @if($company->bank_name && !$isPaid && $invoice->status !== 'Paid')
    <div class="bank-box">
        <strong>Bank Payment Instructions:</strong><br>
        Bank Name: {{ $company->bank_name }}<br>
        Account Name: {{ $company->account_name }}<br>
        Account #: {{ $company->account_number }}<br>
        SWIFT/IFSC: {{ $company->ifsc_swift }}
    </div>
    @endif

    <table class="totals-table">
        <tr>
            <td>Subtotal:</td>
            <td style="text-align: right; font-weight: bold;">{{ $invoice->currency_symbol }}{{ number_format($invoice->subtotal, 2) }}</td>
        </tr>
        @if($invoice->discount_amount > 0)
        <tr>
            <td>Discount:</td>
            <td style="text-align: right; color: #10b981;">-{{ $invoice->currency_symbol }}{{ number_format($invoice->discount_amount, 2) }}</td>
        </tr>
        @endif
        @foreach($invoice->taxes as $tax)
        <tr>
            <td>{{ $tax->tax_name }} ({{ $tax->tax_rate }}%):</td>
            <td style="text-align: right;">+{{ $invoice->currency_symbol }}{{ number_format($tax->tax_amount, 2) }}</td>
        </tr>
        @endforeach
        @if($invoice->additional_charges > 0)
        <tr>
            <td>Additional Charges:</td>
            <td style="text-align: right;">+{{ $invoice->currency_symbol }}{{ number_format($invoice->additional_charges, 2) }}</td>
        </tr>
        @endif
        <tr class="grand-total">
            <td>Grand Total:</td>
            <td style="text-align: right;">{{ $invoice->currency_symbol }}{{ number_format($invoice->grand_total, 2) }}</td>
        </tr>
        <tr>
            <td>Paid Amount:</td>
            <td style="text-align: right; color: #10b981; font-weight: bold;">{{ $invoice->currency_symbol }}{{ number_format($invoice->paid_amount, 2) }}</td>
        </tr>
        <tr style="border-top: 1px solid #333;">
            <td style="font-weight: bold; color: #ef4444;">Balance Due:</td>
            <td style="text-align: right; font-weight: bold; color: #ef4444;">{{ $invoice->currency_symbol }}{{ number_format($invoice->balance_due, 2) }}</td>
        </tr>
    </table>
    <div class="clear"></div>
</div>

@if($invoice->notes)
<div style="margin-top: 25px; font-size: 11px; color: #475569;">
    <strong>Notes:</strong><br>{{ $invoice->notes }}
</div>
@endif

<div class="footer">
    {{ $company->default_invoice_footer ?: 'Thank you for your business — Assertiv Logix Inc.' }}
</div>

</body>
</html>
