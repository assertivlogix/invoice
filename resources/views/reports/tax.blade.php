@extends('layouts.app')

@section('title', 'Tax Collection Report')
@section('page-title', 'Tax Collection Report')

@section('content')
<div class="d-flex align-items-center justify-content-between mb-4">
    <div>
        <a href="{{ route('reports.index') }}" class="text-decoration-none text-muted"><i class="fa-solid fa-arrow-left me-1"></i> Reports Dashboard</a>
        <h4 class="fw-bold mt-2">Tax Collection Summary</h4>
    </div>
    <div class="d-flex gap-2">
        <a href="{{ route('reports.tax', array_merge(request()->all(), ['export' => 'pdf'])) }}" class="btn btn-outline-danger">
            <i class="fa-solid fa-file-pdf me-1"></i> Export PDF
        </a>
        <a href="{{ route('reports.tax', array_merge(request()->all(), ['export' => 'csv'])) }}" class="btn btn-outline-success">
            <i class="fa-solid fa-file-csv me-1"></i> Export CSV
        </a>
    </div>
</div>

<!-- Filters -->
<div class="card-custom p-3 mb-4">
    <form action="{{ route('reports.tax') }}" method="GET" class="row g-3">
        <div class="col-md-4">
            <label class="form-label fs-13 fw-semibold text-primary">Financial Year (FY)</label>
            <select name="financial_year" class="form-select">
                <option value="">-- All Financial Years --</option>
                @foreach($financialYears as $fyKey => $fyName)
                    <option value="{{ $fyKey }}" {{ request('financial_year') == $fyKey ? 'selected' : '' }}>{{ $fyName }}</option>
                @endforeach
            </select>
        </div>
        <div class="col-md-5">
            <label class="form-label fs-13 text-muted">From / To Dates</label>
            <div class="input-group">
                <input type="date" name="date_from" class="form-control" value="{{ $dateFrom }}">
                <input type="date" name="date_to" class="form-control" value="{{ $dateTo }}">
            </div>
        </div>
        <div class="col-md-3 d-flex align-items-end gap-2">
            <button type="submit" class="btn btn-primary w-100">Filter Tax</button>
            <a href="{{ route('reports.tax') }}" class="btn btn-light border" title="Reset Filters"><i class="fa-solid fa-rotate-right"></i></a>
        </div>
    </form>
</div>

<!-- Tax Summary Cards -->
<div class="row g-3 mb-4">
    @foreach($taxSummary as $ts)
    <div class="col-md-3">
        <div class="stat-card">
            <small class="text-muted fs-12">{{ $ts->tax_name }} ({{ $ts->tax_rate }}%)</small>
            <h4 class="fw-bold text-primary mb-0">${{ number_format($ts->total_tax, 2) }}</h4>
        </div>
    </div>
    @endforeach
</div>

<div class="card-custom">
    <div class="table-responsive">
        <table class="table table-custom align-middle">
            <thead>
                <tr>
                    <th>Tax Name</th>
                    <th>Tax Rate</th>
                    <th>Tax Amount</th>
                    <th>Invoice Number</th>
                    <th>Client</th>
                </tr>
            </thead>
            <tbody>
                @forelse($taxRecords as $tr)
                <tr>
                    <td class="fw-bold">{{ $tr->tax_name }}</td>
                    <td>{{ $tr->tax_rate }}%</td>
                    <td class="fw-bold text-primary">${{ number_format($tr->tax_amount, 2) }}</td>
                    <td>
                        @if($tr->invoice)
                        <a href="{{ route('invoices.show', $tr->invoice) }}" class="text-decoration-none">{{ $tr->invoice->invoice_number }}</a>
                        @endif
                    </td>
                    <td>{{ $tr->invoice && $tr->invoice->client ? $tr->invoice->client->company_name : 'N/A' }}</td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="text-center text-muted py-4">No tax records logged.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
