@extends('layouts.app')

@section('title', 'Payment Report')
@section('page-title', 'Payment Transactions Report')

@section('content')
<div class="d-flex align-items-center justify-content-between mb-4">
    <div>
        <a href="{{ route('reports.index') }}" class="text-decoration-none text-muted"><i class="fa-solid fa-arrow-left me-1"></i> Reports Dashboard</a>
        <h4 class="fw-bold mt-2">Payment Transactions Report</h4>
    </div>
    <div class="d-flex gap-2">
        <a href="{{ route('reports.payments', array_merge(request()->all(), ['export' => 'pdf'])) }}" class="btn btn-outline-danger">
            <i class="fa-solid fa-file-pdf me-1"></i> Export PDF
        </a>
        <a href="{{ route('reports.payments', array_merge(request()->all(), ['export' => 'csv'])) }}" class="btn btn-outline-success">
            <i class="fa-solid fa-file-csv me-1"></i> Export CSV
        </a>
    </div>
</div>

<div class="card-custom p-3 mb-4">
    <form action="{{ route('reports.payments') }}" method="GET" class="row g-3">
        <div class="col-md-3">
            <label class="form-label fs-13 fw-semibold text-primary">Financial Year (FY)</label>
            <select name="financial_year" class="form-select">
                <option value="">-- All Financial Years --</option>
                @foreach($financialYears as $fyKey => $fyName)
                    <option value="{{ $fyKey }}" {{ request('financial_year') == $fyKey ? 'selected' : '' }}>{{ $fyName }}</option>
                @endforeach
            </select>
        </div>
        <div class="col-md-3">
            <label class="form-label fs-13 text-muted">Client</label>
            <select name="client_id" class="form-select">
                <option value="">-- All Clients --</option>
                @foreach($clients as $c)
                    <option value="{{ $c->id }}" {{ request('client_id') == $c->id ? 'selected' : '' }}>{{ $c->company_name }}</option>
                @endforeach
            </select>
        </div>
        <div class="col-md-2">
            <label class="form-label fs-13 text-muted">Payment Method</label>
            <select name="payment_method" class="form-select">
                <option value="">-- All Methods --</option>
                <option value="Bank Transfer" {{ request('payment_method') === 'Bank Transfer' ? 'selected' : '' }}>Bank Transfer</option>
                <option value="Credit Card" {{ request('payment_method') === 'Credit Card' ? 'selected' : '' }}>Credit Card</option>
                <option value="Stripe" {{ request('payment_method') === 'Stripe' ? 'selected' : '' }}>Stripe</option>
                <option value="Razorpay" {{ request('payment_method') === 'Razorpay' ? 'selected' : '' }}>Razorpay</option>
                <option value="PayPal" {{ request('payment_method') === 'PayPal' ? 'selected' : '' }}>PayPal</option>
                <option value="UPI" {{ request('payment_method') === 'UPI' ? 'selected' : '' }}>UPI</option>
            </select>
        </div>
        <div class="col-md-2">
            <label class="form-label fs-13 text-muted">From / To Dates</label>
            <div class="input-group input-group-sm">
                <input type="date" name="date_from" class="form-control" value="{{ $dateFrom }}">
                <input type="date" name="date_to" class="form-control" value="{{ $dateTo }}">
            </div>
        </div>
        <div class="col-md-2 d-flex align-items-end gap-2">
            <button type="submit" class="btn btn-primary w-100">Filter</button>
            <a href="{{ route('reports.payments') }}" class="btn btn-light border" title="Reset Filters"><i class="fa-solid fa-rotate-right"></i></a>
        </div>
    </form>
</div>

<div class="card-custom">
    <div class="table-responsive">
        <table class="table table-custom align-middle">
            <thead>
                <tr>
                    <th>Payment ID</th>
                    <th>Client</th>
                    <th>Invoice Number</th>
                    <th>Date</th>
                    <th>Method</th>
                    <th>Transaction ID</th>
                    <th>Amount</th>
                </tr>
            </thead>
            <tbody>
                @forelse($payments as $p)
                <tr>
                    <td class="fw-bold text-primary">{{ $p->payment_id }}</td>
                    <td>{{ $p->client->company_name }}</td>
                    <td>{{ $p->invoice ? $p->invoice->invoice_number : 'N/A' }}</td>
                    <td>{{ $p->payment_date->format('Y-m-d') }}</td>
                    <td><span class="badge bg-light text-dark border">{{ $p->payment_method }}</span></td>
                    <td><code>{{ $p->transaction_id ?: 'N/A' }}</code></td>
                    <td class="fw-bold text-success">+${{ number_format($p->amount, 2) }}</td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" class="text-center text-muted py-4">No records found.</td>
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
