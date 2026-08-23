@extends('layouts.app')

@section('title', 'Record Payment')
@section('page-title', 'Record Client Payment')

@section('content')
<div class="mb-4">
    <a href="{{ route('payments.index') }}" class="text-decoration-none text-muted"><i class="fa-solid fa-arrow-left me-1"></i> Back to Payments</a>
    <h4 class="fw-bold mt-2">Record Payment</h4>
</div>

<div class="row justify-content-center">
    <div class="col-12 col-lg-8">
        <div class="card-custom p-4">
            <form action="{{ route('payments.store') }}" method="POST">
                @csrf
                <div class="row g-3">
                    <div class="col-md-4">
                        <label class="form-label fs-14 fw-medium">Payment ID</label>
                        <input type="text" name="payment_id" class="form-control bg-light" value="{{ $suggestedPaymentId }}" readonly>
                    </div>

                    <div class="col-md-8">
                        <label class="form-label fs-14 fw-medium">Select Unpaid Invoice <span class="text-danger">*</span></label>
                        <select name="invoice_id" id="invoice_id" class="form-select" required>
                            <option value="">-- Select Invoice --</option>
                            @foreach($invoices as $inv)
                                <option value="{{ $inv->id }}" data-balance="{{ $inv->balance_due }}" {{ (old('invoice_id', $selectedInvoice ? $selectedInvoice->id : '') == $inv->id) ? 'selected' : '' }}>
                                    {{ $inv->invoice_number }} &bull; {{ $inv->client->company_name }} (Due: ${{ number_format($inv->balance_due, 2) }})
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-4">
                        <label class="form-label fs-14 fw-medium">Payment Date <span class="text-danger">*</span></label>
                        <input type="date" name="payment_date" class="form-control" value="{{ old('payment_date', date('Y-m-d')) }}" required>
                    </div>

                    <div class="col-md-4">
                        <label class="form-label fs-14 fw-medium">Payment Method <span class="text-danger">*</span></label>
                        <select name="payment_method" class="form-select" required>
                            <option value="Bank Transfer">Bank Transfer</option>
                            <option value="Credit Card">Credit Card</option>
                            <option value="Debit Card">Debit Card</option>
                            <option value="Stripe">Stripe</option>
                            <option value="Razorpay">Razorpay</option>
                            <option value="PayPal">PayPal</option>
                            <option value="UPI">UPI</option>
                            <option value="Cash">Cash</option>
                            <option value="Other">Other</option>
                        </select>
                    </div>

                    <div class="col-md-4">
                        <label class="form-label fs-14 fw-medium">Payment Amount ($) <span class="text-danger">*</span></label>
                        <input type="number" step="0.01" name="amount" id="amount" class="form-control fw-bold text-success fs-16" value="{{ old('amount', $selectedInvoice ? $selectedInvoice->balance_due : '0.00') }}" required>
                        <small class="text-muted d-block mt-1" id="maxBalanceHelp">Max Balance: $0.00</small>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label fs-14 fw-medium">Transaction / Gateway ID</label>
                        <input type="text" name="transaction_id" class="form-control" value="{{ old('transaction_id') }}" placeholder="e.g. ch_3N8x... or bank ref #">
                    </div>

                    <div class="col-md-6">
                        <label class="form-label fs-14 fw-medium">Reference Number</label>
                        <input type="text" name="reference_number" class="form-control" value="{{ old('reference_number') }}" placeholder="Check #, Wire Transfer #">
                    </div>

                    <div class="col-12">
                        <label class="form-label fs-14 fw-medium">Notes</label>
                        <textarea name="notes" class="form-control" rows="3" placeholder="Additional payment details...">{{ old('notes') }}</textarea>
                    </div>

                    <div class="col-12 mt-4">
                        <button type="submit" class="btn btn-success px-4 py-2 fw-bold">
                            <i class="fa-solid fa-circle-check me-1"></i> Confirm & Record Payment
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    document.getElementById('invoice_id')?.addEventListener('change', function () {
        const selected = this.options[this.selectedIndex];
        const balance = selected.getAttribute('data-balance');
        if (balance) {
            document.getElementById('amount').value = parseFloat(balance).toFixed(2);
            document.getElementById('maxBalanceHelp').innerText = 'Max Balance: $' + parseFloat(balance).toFixed(2);
        }
    });
</script>
@endpush
