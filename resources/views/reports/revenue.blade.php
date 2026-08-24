@extends('layouts.app')

@section('title', 'Client Revenue Report')
@section('page-title', 'Client Revenue & Financial Report')

@section('content')
<div class="d-flex align-items-center justify-content-between mb-4">
    <div>
        <a href="{{ route('reports.index') }}" class="text-decoration-none text-muted"><i class="fa-solid fa-arrow-left me-1"></i> Reports Dashboard</a>
        <h4 class="fw-bold mt-2">Client Revenue Breakdown</h4>
    </div>
    <div class="d-flex gap-2">
        <a href="{{ route('reports.revenue', array_merge(request()->all(), ['export' => 'pdf'])) }}" class="btn btn-outline-danger">
            <i class="fa-solid fa-file-pdf me-1"></i> Export PDF
        </a>
        <a href="{{ route('reports.revenue', array_merge(request()->all(), ['export' => 'csv'])) }}" class="btn btn-outline-success">
            <i class="fa-solid fa-file-csv me-1"></i> Export CSV
        </a>
    </div>
</div>

<!-- Filters -->
<div class="card-custom p-3 mb-4">
    <form action="{{ route('reports.revenue') }}" method="GET" class="row g-3">
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
            <button type="submit" class="btn btn-primary w-100">Filter Revenue</button>
            <a href="{{ route('reports.revenue') }}" class="btn btn-light border" title="Reset Filters"><i class="fa-solid fa-rotate-right"></i></a>
        </div>
    </form>
</div>

<div class="card-custom">
    <div class="table-responsive">
        <table class="table table-custom align-middle">
            <thead>
                <tr>
                    <th>Client ID</th>
                    <th>Company Name</th>
                    <th>Total Invoices</th>
                    <th>Total Revenue</th>
                    <th>Paid Amount</th>
                    <th>Outstanding Balance</th>
                </tr>
            </thead>
            <tbody>
                @foreach($clients as $c)
                <tr>
                    <td class="fw-bold text-primary">{{ $c['client_id'] }}</td>
                    <td class="fw-semibold">{{ $c['company_name'] }}</td>
                    <td><span class="badge bg-light text-dark border">{{ $c['total_invoices'] }}</span></td>
                    <td class="fw-bold text-dark">${{ number_format($c['total_revenue'], 2) }}</td>
                    <td class="fw-bold text-success">${{ number_format($c['paid'], 2) }}</td>
                    <td class="fw-bold text-danger">${{ number_format($c['outstanding'], 2) }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection
