<!DOCTYPE html>
<html>
<head>
    <style>
        body { font-family: Arial, sans-serif; background-color: #f4f6f8; padding: 20px; color: #333; }
        .email-container { max-width: 600px; background: #ffffff; padding: 30px; border-radius: 8px; margin: 0 auto; border: 1px solid #e1e4e8; }
        .btn-pay { display: inline-block; background-color: #2563eb; color: #ffffff !important; padding: 12px 24px; text-decoration: none; border-radius: 6px; font-weight: bold; margin-top: 15px; }
    </style>
</head>
<body>
<div class="email-container">
    <h2 style="color: #2563eb; margin-top: 0;">Invoice {{ $invoice->invoice_number }}</h2>
    <p>Dear {{ $client->contact_person ?: $client->company_name }},</p>
    <p>Please find attached invoice <strong>{{ $invoice->invoice_number }}</strong> for your recent services/project with Assertiv Logix.</p>
    
    <div style="background: #f8fafc; padding: 15px; border-radius: 6px; margin: 20px 0;">
        <div><strong>Invoice Amount:</strong> {{ $invoice->currency_symbol }}{{ number_format($invoice->grand_total, 2) }}</div>
        <div><strong>Amount Due:</strong> <span style="color: #ef4444; font-weight: bold;">{{ $invoice->currency_symbol }}{{ number_format($invoice->balance_due, 2) }}</span></div>
        <div><strong>Due Date:</strong> {{ $invoice->due_date->format('M d, Y') }}</div>
    </div>

    @if($paymentLink)
    <p>You can securely complete your payment online using the button below:</p>
    <p><a href="{{ $paymentLink }}" class="btn-pay">Pay Invoice Online</a></p>
    @endif

    <p style="margin-top: 30px; font-size: 13px; color: #666;">
        Thank you for your business!<br>
        <strong>Assertiv Logix Inc.</strong>
    </p>
</div>
</body>
</html>
