@extends('layouts.app')

@section('title', 'Admin Dashboard')
@section('page-title', 'Good day, ' . (auth()->user()->name ?? 'Admin'))

@section('content')
<!-- Top Stat Cards -->
<div class="row g-3 mb-4">
    <div class="col-12 col-sm-6 col-xl-2">
        <div class="stat-card">
            <div class="d-flex align-items-center justify-content-between mb-2">
                <span class="text-muted fs-13 fw-medium">Total Clients</span>
                <div class="stat-icon bg-primary-subtle text-primary">
                    <i class="fa-solid fa-users"></i>
                </div>
            </div>
            <h4 class="fw-bold mb-0 text-dark">{{ $totalClients }}</h4>
            <small class="text-muted fs-12">Active client accounts</small>
        </div>
    </div>

    <div class="col-12 col-sm-6 col-xl-2">
        <div class="stat-card">
            <div class="d-flex align-items-center justify-content-between mb-2">
                <span class="text-muted fs-13 fw-medium">Total Invoiced</span>
                <div class="stat-icon bg-info-subtle text-info">
                    <i class="fa-solid fa-file-invoice-dollar"></i>
                </div>
            </div>
            <h4 class="fw-bold mb-0 text-dark">${{ number_format($totalInvoiced, 2) }}</h4>
            <small class="text-muted fs-12">Lifetime billed value</small>
        </div>
    </div>

    <div class="col-12 col-sm-6 col-xl-2">
        <div class="stat-card">
            <div class="d-flex align-items-center justify-content-between mb-2">
                <span class="text-muted fs-13 fw-medium">Total Paid</span>
                <div class="stat-icon bg-success-subtle text-success">
                    <i class="fa-solid fa-circle-check"></i>
                </div>
            </div>
            <h4 class="fw-bold mb-0 text-success">${{ number_format($totalPaid, 2) }}</h4>
            <small class="text-muted fs-12">Total revenue received</small>
        </div>
    </div>

    <div class="col-12 col-sm-6 col-xl-2">
        <div class="stat-card">
            <div class="d-flex align-items-center justify-content-between mb-2">
                <span class="text-muted fs-13 fw-medium">Outstanding</span>
                <div class="stat-icon bg-warning-subtle text-warning">
                    <i class="fa-solid fa-clock-history"></i>
                </div>
            </div>
            <h4 class="fw-bold mb-0 text-warning">${{ number_format($totalOutstanding, 2) }}</h4>
            <small class="text-muted fs-12">Pending balance due</small>
        </div>
    </div>

    <div class="col-12 col-sm-6 col-xl-2">
        <div class="stat-card">
            <div class="d-flex align-items-center justify-content-between mb-2">
                <span class="text-muted fs-13 fw-medium">Overdue</span>
                <div class="stat-icon bg-danger-subtle text-danger">
                    <i class="fa-solid fa-triangle-exclamation"></i>
                </div>
            </div>
            <h4 class="fw-bold mb-0 text-danger">${{ number_format($totalOverdue, 2) }}</h4>
            <small class="text-muted fs-12">Requires attention</small>
        </div>
    </div>

    <div class="col-12 col-sm-6 col-xl-2">
        <div class="stat-card">
            <div class="d-flex align-items-center justify-content-between mb-2">
                <span class="text-muted fs-13 fw-medium">This Month</span>
                <div class="stat-icon bg-purple-subtle text-purple" style="background:#f3e8ff; color:#9333ea;">
                    <i class="fa-solid fa-calendar-days"></i>
                </div>
            </div>
            <h4 class="fw-bold mb-0 text-dark">{{ $thisMonthInvoices }}</h4>
            <small class="text-muted fs-12">Invoices created</small>
        </div>
    </div>
</div>

<!-- Charts Row -->
<div class="row g-3 mb-4">
    <!-- Revenue Bar Chart -->
    <div class="col-12 col-lg-8">
        <div class="card-custom p-4 h-100">
            <div class="d-flex align-items-center justify-content-between mb-3">
                <div>
                    <h6 class="fw-bold mb-1">Revenue Overview</h6>
                    <small class="text-muted">Monthly Invoiced vs. Payments Received</small>
                </div>
                <span class="badge bg-light text-muted border">Last 6 Months</span>
            </div>
            <div style="height: 280px;">
                <canvas id="revenueChart"></canvas>
            </div>
        </div>
    </div>

    <!-- Invoice Status Donut Chart -->
    <div class="col-12 col-lg-4">
        <div class="card-custom p-4 h-100">
            <div class="d-flex align-items-center justify-content-between mb-3">
                <div>
                    <h6 class="fw-bold mb-1">Invoice Status Breakdown</h6>
                    <small class="text-muted">Distribution of invoice statuses</small>
                </div>
            </div>
            <div style="height: 260px;" class="d-flex align-items-center justify-content-center">
                <canvas id="statusChart"></canvas>
            </div>
        </div>
    </div>
</div>

<!-- Recent Tables & Activity Grid -->
<div class="row g-3">
    <!-- Recent Invoices -->
    <div class="col-12 col-lg-7">
        <div class="card-custom">
            <div class="p-3 border-bottom d-flex align-items-center justify-content-between">
                <h6 class="fw-bold mb-0"><i class="fa-solid fa-file-invoice text-primary me-2"></i> Recent Invoices</h6>
                <a href="{{ route('invoices.index') }}" class="btn btn-sm btn-link text-decoration-none">View All <i class="fa-solid fa-arrow-right"></i></a>
            </div>
            <div class="table-responsive">
                <table class="table table-custom align-middle">
                    <thead>
                        <tr>
                            <th>Invoice #</th>
                            <th>Client</th>
                            <th>Date</th>
                            <th>Amount</th>
                            <th>Status</th>
                            <th class="text-end">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($recentInvoices as $inv)
                        <tr>
                            <td class="fw-semibold">
                                <a href="{{ route('invoices.show', $inv) }}" class="text-decoration-none text-dark">{{ $inv->invoice_number }}</a>
                            </td>
                            <td>{{ $inv->client->company_name }}</td>
                            <td>{{ $inv->invoice_date->format('M d, Y') }}</td>
                            <td class="fw-bold">{{ $inv->currency_symbol }}{{ number_format($inv->grand_total, 2) }}</td>
                            <td>
                                <span class="badge badge-status badge-{{ strtolower(str_replace(' ', '', $inv->status)) }}">
                                    {{ $inv->status }}
                                </span>
                            </td>
                            <td class="text-end">
                                <a href="{{ route('invoices.show', $inv) }}" class="btn btn-sm btn-light border"><i class="fa-regular fa-eye"></i></a>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="text-center text-muted py-4">No invoices found.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Recent Payments -->
    <div class="col-12 col-lg-5">
        <div class="card-custom">
            <div class="p-3 border-bottom d-flex align-items-center justify-content-between">
                <h6 class="fw-bold mb-0"><i class="fa-solid fa-credit-card text-success me-2"></i> Recent Payments</h6>
                <a href="{{ route('payments.index') }}" class="btn btn-sm btn-link text-decoration-none">View All <i class="fa-solid fa-arrow-right"></i></a>
            </div>
            <div class="table-responsive">
                <table class="table table-custom align-middle">
                    <thead>
                        <tr>
                            <th>Payment #</th>
                            <th>Client</th>
                            <th>Amount</th>
                            <th>Date</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($recentPayments as $pay)
                        <tr>
                            <td class="fw-semibold">{{ $pay->payment_id }}</td>
                            <td>{{ $pay->client->company_name }}</td>
                            <td class="fw-bold text-success">+${{ number_format($pay->amount, 2) }}</td>
                            <td>{{ $pay->payment_date->format('M d') }}</td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="4" class="text-center text-muted py-4">No recent payments.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Top Clients -->
    <div class="col-12 col-lg-7">
        <div class="card-custom">
            <div class="p-3 border-bottom d-flex align-items-center justify-content-between">
                <h6 class="fw-bold mb-0"><i class="fa-solid fa-crown text-warning me-2"></i> Top Clients by Revenue</h6>
                <a href="{{ route('clients.index') }}" class="btn btn-sm btn-link text-decoration-none">All Clients</a>
            </div>
            <div class="table-responsive">
                <table class="table table-custom align-middle">
                    <thead>
                        <tr>
                            <th>Client Name</th>
                            <th>Invoices</th>
                            <th>Total Billed</th>
                            <th>Paid Amount</th>
                            <th>Outstanding</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($topClients as $client)
                        <tr>
                            <td class="fw-bold">
                                <a href="{{ route('clients.show', $client) }}" class="text-decoration-none text-dark">{{ $client->company_name }}</a>
                            </td>
                            <td><span class="badge bg-light text-dark border">{{ $client->invoices_count }}</span></td>
                            <td class="fw-semibold">${{ number_format($client->total_invoiced, 2) }}</td>
                            <td class="text-success fw-semibold">${{ number_format($client->total_paid, 2) }}</td>
                            <td class="text-danger fw-semibold">${{ number_format($client->total_outstanding, 2) }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Activity Log Timeline -->
    <div class="col-12 col-lg-5">
        <div class="card-custom">
            <div class="p-3 border-bottom">
                <h6 class="fw-bold mb-0"><i class="fa-solid fa-clock-rotate-left text-info me-2"></i> Activity Timeline</h6>
            </div>
            <div class="p-3" style="max-height: 280px; overflow-y: auto;">
                <ul class="list-unstyled mb-0">
                    @forelse($recentActivities as $act)
                    <li class="d-flex mb-3 pb-2 border-bottom border-light">
                        <div class="me-3 mt-1">
                            <span class="badge rounded-pill bg-light text-primary border"><i class="fa-solid fa-info"></i></span>
                        </div>
                        <div>
                            <div class="fs-13 fw-semibold text-dark">{{ $act->description }}</div>
                            <small class="text-muted fs-12">{{ $act->created_at->diffForHumans() }}</small>
                        </div>
                    </li>
                    @empty
                    <li class="text-muted text-center py-3">No activity records.</li>
                    @endforelse
                </ul>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    // Revenue Chart
    const ctxRev = document.getElementById('revenueChart').getContext('2d');
    new Chart(ctxRev, {
        type: 'bar',
        data: {
            labels: {!! json_encode($months) !!},
            datasets: [
                {
                    label: 'Invoiced ($)',
                    data: {!! json_encode($invoicedData) !!},
                    backgroundColor: '#3b82f6',
                    borderRadius: 6,
                },
                {
                    label: 'Paid ($)',
                    data: {!! json_encode($paidData) !!},
                    backgroundColor: '#10b981',
                    borderRadius: 6,
                }
            ]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: { legend: { position: 'top' } },
            scales: {
                y: { beginAtZero: true }
            }
        }
    });

    // Status Donut Chart
    const ctxStatus = document.getElementById('statusChart').getContext('2d');
    new Chart(ctxStatus, {
        type: 'doughnut',
        data: {
            labels: ['Draft', 'Sent', 'Partial', 'Paid', 'Overdue', 'Cancelled'],
            datasets: [{
                data: [
                    {{ $statusCounts['Draft'] }},
                    {{ $statusCounts['Sent'] }},
                    {{ $statusCounts['Partially Paid'] }},
                    {{ $statusCounts['Paid'] }},
                    {{ $statusCounts['Overdue'] }},
                    {{ $statusCounts['Cancelled'] }}
                ],
                backgroundColor: ['#94a3b8', '#3b82f6', '#f59e0b', '#10b981', '#ef4444', '#64748b']
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: { legend: { position: 'bottom' } }
        }
    });
</script>
@endpush
