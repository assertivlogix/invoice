@extends('layouts.public')

@section('title', 'Payment Successful')

@section('content')
<div class="row justify-content-center">
    <div class="col-12 col-md-8 col-lg-6">
        <div class="checkout-card text-center p-5">
            <div class="mb-3 text-success">
                <i class="fa-solid fa-circle-check" style="font-size: 4rem;"></i>
            </div>
            <h3 class="fw-bold text-dark mb-1">Payment Successful!</h3>
            <p class="text-muted fs-15 mb-4">Thank you for your payment. Your transaction has been confirmed and recorded.</p>

            <div class="bg-light p-4 rounded-3 text-start mb-4">
                <div class="d-flex justify-content-between mb-2">
                    <span class="text-muted">Invoice Number:</span>
                    <strong class="text-dark">{{ $invoice->invoice_number }}</strong>
                </div>
                <div class="d-flex justify-content-between mb-2">
                    <span class="text-muted">Client Name:</span>
                    <strong class="text-dark">{{ $invoice->client->company_name }}</strong>
                </div>
                <div class="d-flex justify-content-between mb-2">
                    <span class="text-muted">Amount Paid:</span>
                    <strong class="text-success">{{ $invoice->currency_symbol }}{{ number_format($latestPayment ? $latestPayment->amount : $invoice->grand_total, 2) }}</strong>
                </div>
                @if($latestPayment)
                <div class="d-flex justify-content-between mb-2">
                    <span class="text-muted">Payment Reference:</span>
                    <code>{{ $latestPayment->payment_id }}</code>
                </div>
                @endif
                <div class="d-flex justify-content-between">
                    <span class="text-muted">Invoice Status:</span>
                    <span class="badge bg-success text-white fw-bold">PAID</span>
                </div>
            </div>

            <div class="d-grid gap-2">
                <a href="{{ route('public.payment.pdf', $link->token) }}" class="btn btn-success btn-lg py-2 fw-bold">
                    <i class="fa-solid fa-file-pdf me-2"></i> Download Watermarked Paid Invoice (PDF)
                </a>
            </div>
        </div>
    </div>
</div>
@endsection
