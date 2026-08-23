@extends('layouts.public')

@section('title', 'Link Invalid')

@section('content')
<div class="row justify-content-center">
    <div class="col-12 col-md-8 col-lg-6">
        <div class="checkout-card text-center p-5">
            <div class="mb-3 text-warning">
                <i class="fa-solid fa-link-slash" style="font-size: 4rem;"></i>
            </div>
            <h4 class="fw-bold text-dark mb-1">Payment Link Expired or Invalid</h4>
            <p class="text-muted fs-15 mb-0">This payment link has expired, been cancelled, or already used. Please request a new payment link from your account representative.</p>
        </div>
    </div>
</div>
@endsection
