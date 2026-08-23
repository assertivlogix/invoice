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
    <h2 style="color: {{ $isOverdue ? '#ef4444' : '#f59e0b' }}; margin-top: 0;">
        {{ $isOverdue ? 'Overdue Payment Reminder' : 'Payment Reminder' }}
    </h2>
    <p>Dear {{ $client->contact_person ?: $client->company_name }},</p>
    <p>This is a friendly reminder regarding invoice <strong>{{ $invoice->invoice_number }}</strong> which {{ $isOverdue ? 'is currently overdue' : 'is due soon' }}.</p>
    
    <div style="background: #f8fafc; padding: 15px; border-radius: 6px; margin: 20px 0;">
        <div><strong>Invoice Number:</strong> {{ $invoice->invoice_number }}</div>
        <div><strong>Outstanding Amount:</strong> <span style="color: #ef4444; font-weight: bold;">{{ $invoice->currency_symbol }}{{ number_format($invoice->balance_due, 2) }}</span></div>
        <div><strong>Due Date:</strong> {{ $invoice->due_date->format('M d, Y') }}</div>
    </div>

    @if($paymentLink)
    <p>Please use the secure button below to complete your payment:</p>
    <p><a href="{{ $paymentLink }}" class="btn-pay">Pay Outstanding Balance Now</a></p>
    @endif

    <p style="margin-top: 30px; font-size: 13px; color: #666;">
        If you have already processed this payment, please disregard this notice.<br>
        <strong>Assertiv Logix Inc.</strong>
    </p>
</div>
</body>
</html>
