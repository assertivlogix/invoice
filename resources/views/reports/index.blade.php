@extends('layouts.app')

@section('title', 'Financial Reports')
@section('page-title', 'Financial & Analytics Reports')

@section('content')
<div class="d-flex align-items-center justify-content-between mb-3">
    <div>
        <h5 class="fw-bold mb-1">Reports Dashboard</h5>
        <small class="text-muted">Generate and export financial insights by Financial Year</small>
    </div>
</div>

<!-- Financial Year Selector Card -->
<div class="card-custom p-3 mb-4 bg-white border shadow-sm rounded-3">
    <form action="{{ route('reports.index') }}" method="GET" class="row g-3 align-items-center">
        <div class="col-md-4">
            <label class="form-label fs-13 fw-bold text-dark mb-1"><i class="fa-solid fa-calendar-days text-primary me-1"></i> Select Financial Year (FY)</label>
            <select name="financial_year" class="form-select fw-medium" onchange="this.form.submit()">
                <option value="">-- All Financial Years --</option>
                @foreach($financialYears as $fyKey => $fyName)
                    <option value="{{ $fyKey }}" {{ request('financial_year') == $fyKey ? 'selected' : '' }}>{{ $fyName }}</option>
                @endforeach
            </select>
        </div>
        <div class="col-md-8 text-end">
            <span class="fs-13 text-muted me-2 d-none d-md-inline">Quick PDF Exports (FY):</span>
            <div class="btn-group" role="group">
                <a href="{{ route('reports.invoices', array_filter(['financial_year' => request('financial_year'), 'export' => 'pdf'])) }}" class="btn btn-sm btn-outline-danger"><i class="fa-solid fa-file-pdf me-1"></i> Invoices</a>
                <a href="{{ route('reports.payments', array_filter(['financial_year' => request('financial_year'), 'export' => 'pdf'])) }}" class="btn btn-sm btn-outline-success"><i class="fa-solid fa-file-pdf me-1"></i> Payments</a>
                <a href="{{ route('reports.revenue', array_filter(['financial_year' => request('financial_year'), 'export' => 'pdf'])) }}" class="btn btn-sm btn-outline-warning text-dark"><i class="fa-solid fa-file-pdf me-1"></i> Revenue</a>
                <a href="{{ route('reports.tax', array_filter(['financial_year' => request('financial_year'), 'export' => 'pdf'])) }}" class="btn btn-sm btn-outline-info"><i class="fa-solid fa-file-pdf me-1"></i> Tax</a>
            </div>
        </div>
    </form>
</div>

<div class="row g-4">
    <div class="col-12 col-md-6 col-xl-3">
        <div class="card-custom p-4 h-100 d-flex flex-column justify-content-between">
            <div>
                <div class="stat-icon bg-primary-subtle text-primary mb-3">
                    <i class="fa-solid fa-file-invoice"></i>
                </div>
                <h5 class="fw-bold mb-1">Invoice Report</h5>
                <p class="text-muted fs-14">Filter invoices by client, date range, financial year, currency and payment status.</p>
            </div>
            <div class="d-flex gap-2 mt-3">
                <a href="{{ route('reports.invoices', array_filter(['financial_year' => request('financial_year')])) }}" class="btn btn-outline-primary w-100">View Report</a>
                <a href="{{ route('reports.invoices', array_filter(['financial_year' => request('financial_year'), 'export' => 'csv'])) }}" class="btn btn-light border" title="Export CSV"><i class="fa-solid fa-file-csv text-success"></i></a>
                <a href="{{ route('reports.invoices', array_filter(['financial_year' => request('financial_year'), 'export' => 'pdf'])) }}" class="btn btn-light border" title="Export PDF"><i class="fa-solid fa-file-pdf text-danger"></i></a>
            </div>
        </div>
    </div>

    <div class="col-12 col-md-6 col-xl-3">
        <div class="card-custom p-4 h-100 d-flex flex-column justify-content-between">
            <div>
                <div class="stat-icon bg-success-subtle text-success mb-3">
                    <i class="fa-solid fa-credit-card"></i>
                </div>
                <h5 class="fw-bold mb-1">Payment Report</h5>
                <p class="text-muted fs-14">Detailed payment transaction logs grouped by method, gateway, client and FY.</p>
            </div>
            <div class="d-flex gap-2 mt-3">
                <a href="{{ route('reports.payments', array_filter(['financial_year' => request('financial_year')])) }}" class="btn btn-outline-success w-100">View Report</a>
                <a href="{{ route('reports.payments', array_filter(['financial_year' => request('financial_year'), 'export' => 'csv'])) }}" class="btn btn-light border" title="Export CSV"><i class="fa-solid fa-file-csv text-success"></i></a>
                <a href="{{ route('reports.payments', array_filter(['financial_year' => request('financial_year'), 'export' => 'pdf'])) }}" class="btn btn-light border" title="Export PDF"><i class="fa-solid fa-file-pdf text-danger"></i></a>
            </div>
        </div>
    </div>

    <div class="col-12 col-md-6 col-xl-3">
        <div class="card-custom p-4 h-100 d-flex flex-column justify-content-between">
            <div>
                <div class="stat-icon bg-warning-subtle text-warning mb-3">
                    <i class="fa-solid fa-chart-line"></i>
                </div>
                <h5 class="fw-bold mb-1">Client Revenue</h5>
                <p class="text-muted fs-14">Revenue contribution, paid amounts and outstanding balances per client per FY.</p>
            </div>
            <div class="d-flex gap-2 mt-3">
                <a href="{{ route('reports.revenue', array_filter(['financial_year' => request('financial_year')])) }}" class="btn btn-outline-warning w-100 text-dark">View Report</a>
                <a href="{{ route('reports.revenue', array_filter(['financial_year' => request('financial_year'), 'export' => 'csv'])) }}" class="btn btn-light border" title="Export CSV"><i class="fa-solid fa-file-csv text-success"></i></a>
                <a href="{{ route('reports.revenue', array_filter(['financial_year' => request('financial_year'), 'export' => 'pdf'])) }}" class="btn btn-light border" title="Export PDF"><i class="fa-solid fa-file-pdf text-danger"></i></a>
            </div>
        </div>
    </div>

    <div class="col-12 col-md-6 col-xl-3">
        <div class="card-custom p-4 h-100 d-flex flex-column justify-content-between">
            <div>
                <div class="stat-icon bg-info-subtle text-info mb-3">
                    <i class="fa-solid fa-percent"></i>
                </div>
                <h5 class="fw-bold mb-1">Tax Report</h5>
                <p class="text-muted fs-14">Summary of tax collected (GST, VAT, Sales Tax) grouped by tax type and financial year.</p>
            </div>
            <div class="d-flex gap-2 mt-3">
                <a href="{{ route('reports.tax', array_filter(['financial_year' => request('financial_year')])) }}" class="btn btn-outline-info w-100">View Report</a>
                <a href="{{ route('reports.tax', array_filter(['financial_year' => request('financial_year'), 'export' => 'csv'])) }}" class="btn btn-light border" title="Export CSV"><i class="fa-solid fa-file-csv text-success"></i></a>
                <a href="{{ route('reports.tax', array_filter(['financial_year' => request('financial_year'), 'export' => 'pdf'])) }}" class="btn btn-light border" title="Export PDF"><i class="fa-solid fa-file-pdf text-danger"></i></a>
            </div>
        </div>
    </div>
</div>
@endsection
