@extends('layouts.app')

@section('title', 'Financial Reports')
@section('page-title', 'Financial & Analytics Reports')

@section('content')
<div class="mb-4">
    <h5 class="fw-bold mb-1">Reports Dashboard</h5>
    <small class="text-muted">Generate and export financial insights</small>
</div>

<div class="row g-4">
    <div class="col-12 col-md-6 col-xl-3">
        <div class="card-custom p-4 h-100 d-flex flex-column justify-content-between">
            <div>
                <div class="stat-icon bg-primary-subtle text-primary mb-3">
                    <i class="fa-solid fa-file-invoice"></i>
                </div>
                <h5 class="fw-bold mb-1">Invoice Report</h5>
                <p class="text-muted fs-14">Filter invoices by client, date range, currency and payment status.</p>
            </div>
            <div class="d-flex gap-2">
                <a href="{{ route('reports.invoices') }}" class="btn btn-outline-primary w-100">View Report</a>
                <a href="{{ route('reports.invoices', ['export' => 'csv']) }}" class="btn btn-light border" title="Export CSV"><i class="fa-solid fa-download"></i></a>
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
                <p class="text-muted fs-14">Detailed payment transaction logs grouped by method, gateway and client.</p>
            </div>
            <div class="d-flex gap-2">
                <a href="{{ route('reports.payments') }}" class="btn btn-outline-success w-100">View Report</a>
                <a href="{{ route('reports.payments', ['export' => 'csv']) }}" class="btn btn-light border" title="Export CSV"><i class="fa-solid fa-download"></i></a>
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
                <p class="text-muted fs-14">Revenue contribution, paid amounts and outstanding balances per client.</p>
            </div>
            <div class="d-flex gap-2">
                <a href="{{ route('reports.revenue') }}" class="btn btn-outline-warning w-100 text-dark">View Report</a>
                <a href="{{ route('reports.revenue', ['export' => 'csv']) }}" class="btn btn-light border" title="Export CSV"><i class="fa-solid fa-download"></i></a>
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
                <p class="text-muted fs-14">Summary of tax collected (GST, VAT, Sales Tax) grouped by tax type.</p>
            </div>
            <div class="d-flex gap-2">
                <a href="{{ route('reports.tax') }}" class="btn btn-outline-info w-100">View Report</a>
                <a href="{{ route('reports.tax', ['export' => 'csv']) }}" class="btn btn-light border" title="Export CSV"><i class="fa-solid fa-download"></i></a>
            </div>
        </div>
    </div>
</div>
@endsection
