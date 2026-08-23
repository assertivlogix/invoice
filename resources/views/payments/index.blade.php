@extends('layouts.app')

@section('title', 'Payments')
@section('page-title', 'Payment Ledger')

@section('content')
<div class="d-flex align-items-center justify-content-between mb-4">
    <div>
        <h5 class="fw-bold mb-1">Payment Transactions</h5>
        <small class="text-muted">History of manual and online payments received</small>
    </div>
    <a href="{{ route('payments.create') }}" class="btn btn-primary">
        <i class="fa-solid fa-plus me-1"></i> Record Payment
    </a>
</div>

<!-- Filters -->
<div class="card-custom p-3 mb-4">
    <form action="{{ route('payments.index') }}" method="GET" class="row g-3">
        <div class="col-md-4">
            <input type="text" name="search" class="form-control" placeholder="Search Payment ID, Txn ID, Ref..." value="{{ request('search') }}">
        </div>
        <div class="col-md-3">
            <select name="client_id" class="form-select">
                <option value="">-- All Clients --</option>
                @foreach($clients as $c)
                    <option value="{{ $c->id }}" {{ request('client_id') == $c->id ? 'selected' : '' }}>{{ $c->company_name }}</option>
                @endforeach
            </select>
        </div>
        <div class="col-md-3">
            <select name="payment_method" class="form-select">
                <option value="">-- All Payment Methods --</option>
                <option value="Bank Transfer">Bank Transfer</option>
                <option value="Credit Card">Credit Card</option>
                <option value="Stripe">Stripe</option>
                <option value="Razorpay">Razorpay</option>
                <option value="PayPal">PayPal</option>
                <option value="UPI">UPI</option>
                <option value="Cash">Cash</option>
            </select>
        </div>
        <div class="col-md-2 d-flex gap-2">
            <button type="submit" class="btn btn-primary w-100">Filter</button>
            <a href="{{ route('payments.index') }}" class="btn btn-light border"><i class="fa-solid fa-rotate-right"></i></a>
        </div>
    </form>
</div>

<!-- Payments Table -->
<div class="card-custom">
    <div class="table-responsive">
        <table class="table table-custom align-middle">
            <thead>
                <tr>
                    <th>Payment ID</th>
                    <th>Invoice #</th>
                    <th>Client</th>
                    <th>Date</th>
                    <th>Method</th>
                    <th>Transaction ID</th>
                    <th>Amount</th>
                    <th>Recorded By</th>
                </tr>
            </thead>
            <tbody>
                @forelse($payments as $pay)
                <tr>
                    <td class="fw-bold text-primary">{{ $pay->payment_id }}</td>
                    <td class="fw-semibold">
                        @if($pay->invoice)
                            <a href="{{ route('invoices.show', $pay->invoice) }}" class="text-decoration-none text-dark">{{ $pay->invoice->invoice_number }}</a>
                        @else N/A @endif
                    </td>
                    <td><a href="{{ route('clients.show', $pay->client) }}" class="text-decoration-none text-dark">{{ $pay->client->company_name }}</a></td>
                    <td>{{ $pay->payment_date->format('M d, Y') }}</td>
                    <td><span class="badge bg-light text-dark border">{{ $pay->payment_method }}</span></td>
                    <td><code>{{ $pay->transaction_id ?: 'N/A' }}</code></td>
                    <td class="fw-bold text-success">+${{ number_format($pay->amount, 2) }}</td>
                    <td class="fs-13 text-muted">{{ $pay->createdBy ? $pay->createdBy->name : 'System' }}</td>
                </tr>
                @empty
                <tr>
                    <td colspan="8" class="text-center text-muted py-4">No payment transactions recorded.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="p-3 border-top">
        {{ $payments->links() }}
    </div>
</div>
@endsection
