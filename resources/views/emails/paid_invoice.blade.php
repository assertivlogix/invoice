<!DOCTYPE html>
<html>
<head>
    <style>
        body { font-family: Arial, sans-serif; background-color: #f4f6f8; padding: 20px; color: #333; }
        .email-container { max-width: 600px; background: #ffffff; padding: 30px; border-radius: 8px; margin: 0 auto; border: 1px solid #e1e4e8; }
        .badge-paid { background-color: #10b981; color: #ffffff; padding: 6px 16px; border-radius: 20px; font-weight: bold; }
    </style>
</head>
<body>
<div class="email-container">
    <div style="text-align: right;"><span class="badge-paid">PAID</span></div>
    <h2 style="color: #10b981; margin-top: 0;">Payment Confirmation</h2>
    <p>Dear {{ $client->contact_person ?: $client->company_name }},</p>
    <p>Thank you for your payment! We have successfully received your payment for invoice <strong>{{ $invoice->invoice_number }}</strong>.</p>
    
    <div style="background: #f0fdf4; padding: 15px; border-radius: 6px; margin: 20px 0; border: 1px solid #bbf7d0;">
        <div><strong>Invoice Total:</strong> {{ $invoice->currency_symbol }}{{ number_format($invoice->grand_total, 2) }}</div>
        <div><strong>Payment Received:</strong> {{ $invoice->currency_symbol }}{{ number_format($payment->amount, 2) }}</div>
        <div><strong>Remaining Balance:</strong> {{ $invoice->currency_symbol }}0.00</div>
        <div><strong>Payment Reference:</strong> <code>{{ $payment->payment_id }}</code></div>
        <div><strong>Payment Method:</strong> {{ $payment->payment_method }}</div>
    </div>

    <p>Please find your official <strong>watermarked PAID Invoice PDF</strong> attached to this email.</p>

    <p style="margin-top: 30px; font-size: 13px; color: #666;">
        Thank you for your business!<br>
        <strong>Assertiv Logix Inc.</strong>
    </p>
</div>
</body>
</html>
