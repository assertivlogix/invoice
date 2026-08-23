@extends('layouts.public')

@section('title', 'Payment Failed')

@section('content')
<div class="row justify-content-center">
    <div class="col-12 col-md-8 col-lg-6">
        <div class="checkout-card text-center p-5">
            <div class="mb-3 text-danger">
                <i class="fa-solid fa-circle-xmark" style="font-size: 4rem;"></i>
            </div>
            <h3 class="fw-bold text-dark mb-1">Payment Unsuccessful</h3>
            <p class="text-muted fs-15 mb-4">Your payment attempt could not be processed. No funds were charged.</p>

            @if($link)
            <a href="{{ route('public.payment.checkout', $link->token) }}" class="btn btn-primary btn-lg py-2 fw-bold">
                <i class="fa-solid fa-rotate-left me-2"></i> Try Payment Again
            </a>
            @endif
        </div>
    </div>
</div>
@endsection
