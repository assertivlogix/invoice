@extends('layouts.app')

@section('title', 'Invoice Report')
@section('page-title', 'Invoice Financial Report')

@section('content')
<div class="d-flex align-items-center justify-content-between mb-4">
    <div>
        <a href="{{ route('reports.index') }}" class="text-decoration-none text-muted"><i class="fa-solid fa-arrow-left me-1"></i> Reports Dashboard</a>
        <h4 class="fw-bold mt-2">Invoice Financial Report</h4>
    </div>
    <a href="{{ route('reports.invoices', array_merge(request()->all(), ['export' => 'csv'])) }}" class="btn btn-outline-success">
        <i class="fa-solid fa-file-csv me-1"></i> Export CSV
    </a>
</div>

<!-- Filters -->
<div class="card-custom p-3 mb-4">
    <form action="{{ route('reports.invoices') }}" method="GET" class="row g-3">
        <div class="col-md-3">
            <label class="form-label fs-13 text-muted">Client</label>
            <select name="client_id" class="form-select">
                <option value="">-- All Clients --</option>
                @foreach($clients as $c)
                    <option value="{{ $c->id }}" {{ request('client_id') == $c->id ? 'selected' : '' }}>{{ $c->company_name }}</option>
                @endforeach
            </select>
        </div>
        <div class="col-md-3">
            <label class="form-label fs-13 text-muted">Status</label>
            <select name="status" class="form-select">
                <option value="">-- All Statuses --</option>
                <option value="Draft" {{ request('status') === 'Draft' ? 'selected' : '' }}>Draft</option>
                <option value="Sent" {{ request('status') === 'Sent' ? 'selected' : '' }}>Sent</option>
                <option value="Paid" {{ request('status') === 'Paid' ? 'selected' : '' }}>Paid</option>
                <option value="Partially Paid" {{ request('status') === 'Partially Paid' ? 'selected' : '' }}>Partially Paid</option>
                <option value="Overdue" {{ request('status') === 'Overdue' ? 'selected' : '' }}>Overdue</option>
            </select>
        </div>
        <div class="col-md-2">
            <label class="form-label fs-13 text-muted">From Date</label>
            <input type="date" name="date_from" class="form-control" value="{{ request('date_from') }}">
        </div>
        <div class="col-md-2">
            <label class="form-label fs-13 text-muted">To Date</label>
            <input type="date" name="date_to" class="form-control" value="{{ request('date_to') }}">
        </div>
        <div class="col-md-2 d-flex align-items-end gap-2">
            <button type="submit" class="btn btn-primary w-100">Filter</button>
            <a href="{{ route('reports.invoices') }}" class="btn btn-light border"><i class="fa-solid fa-rotate-right"></i></a>
        </div>
    </form>
</div>

<!-- Summary Row -->
<div class="row g-3 mb-4">
    <div class="col-md-3">
        <div class="stat-card">
            <small class="text-muted fs-12">Total Invoices</small>
            <h4 class="fw-bold mb-0">{{ $summary['count'] }}</h4>
        </div>
    </div>
    <div class="col-md-3">
        <div class="stat-card">
            <small class="text-muted fs-12">Total Billed Value</small>
            <h4 class="fw-bold text-primary mb-0">${{ number_format($summary['total'], 2) }}</h4>
        </div>
    </div>
    <div class="col-md-3">
        <div class="stat-card">
            <small class="text-muted fs-12">Total Collected</small>
            <h4 class="fw-bold text-success mb-0">${{ number_format($summary['paid'], 2) }}</h4>
        </div>
    </div>
    <div class="col-md-3">
        <div class="stat-card">
            <small class="text-muted fs-12">Total Outstanding</small>
            <h4 class="fw-bold text-danger mb-0">${{ number_format($summary['balance'], 2) }}</h4>
        </div>
    </div>
</div>

<div class="card-custom">
    <div class="table-responsive">
        <table class="table table-custom align-middle">
            <thead>
                <tr>
                    <th>Invoice #</th>
                    <th>Client</th>
                    <th>Invoice Date</th>
                    <th>Due Date</th>
                    <th>Total</th>
                    <th>Paid</th>
                    <th>Balance</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                @forelse($invoices as $inv)
                <tr>
                    <td class="fw-bold"><a href="{{ route('invoices.show', $inv) }}" class="text-decoration-none text-dark">{{ $inv->invoice_number }}</a></td>
                    <td>{{ $inv->client->company_name }}</td>
                    <td>{{ $inv->invoice_date->format('Y-m-d') }}</td>
                    <td>{{ $inv->due_date->format('Y-m-d') }}</td>
                    <td class="fw-bold">${{ number_format($inv->grand_total, 2) }}</td>
                    <td class="text-success">${{ number_format($inv->paid_amount, 2) }}</td>
                    <td class="text-danger fw-semibold">${{ number_format($inv->balance_due, 2) }}</td>
                    <td><span class="badge badge-status badge-{{ strtolower(str_replace(' ', '', $inv->status)) }}">{{ $inv->status }}</span></td>
                </tr>
                @empty
                <tr>
                    <td colspan="8" class="text-center text-muted py-4">No records found.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="p-3 border-top">
        {{ $invoices->links() }}
    </div>
</div>
@endsection
