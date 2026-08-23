@extends('layouts.app')

@section('title', 'Client Revenue Report')
@section('page-title', 'Client Revenue & Financial Report')

@section('content')
<div class="d-flex align-items-center justify-content-between mb-4">
    <div>
        <a href="{{ route('reports.index') }}" class="text-decoration-none text-muted"><i class="fa-solid fa-arrow-left me-1"></i> Reports Dashboard</a>
        <h4 class="fw-bold mt-2">Client Revenue Breakdown</h4>
    </div>
    <a href="{{ route('reports.revenue', ['export' => 'csv']) }}" class="btn btn-outline-success">
        <i class="fa-solid fa-file-csv me-1"></i> Export CSV
    </a>
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
