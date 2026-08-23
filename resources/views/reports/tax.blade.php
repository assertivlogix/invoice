@extends('layouts.app')

@section('title', 'Tax Collection Report')
@section('page-title', 'Tax Collection Report')

@section('content')
<div class="d-flex align-items-center justify-content-between mb-4">
    <div>
        <a href="{{ route('reports.index') }}" class="text-decoration-none text-muted"><i class="fa-solid fa-arrow-left me-1"></i> Reports Dashboard</a>
        <h4 class="fw-bold mt-2">Tax Collection Summary</h4>
    </div>
    <a href="{{ route('reports.tax', ['export' => 'csv']) }}" class="btn btn-outline-success">
        <i class="fa-solid fa-file-csv me-1"></i> Export CSV
    </a>
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
