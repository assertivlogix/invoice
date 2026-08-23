@extends('layouts.app')

@section('title', $client->company_name . ' — Client Dashboard')
@section('page-title', 'Client Dashboard: ' . $client->company_name)

@section('content')
<!-- Header Banner -->
<div class="card-custom p-4 mb-4">
    <div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3">
        <div class="d-flex align-items-center gap-3">
            <div class="avatar bg-primary-subtle text-primary rounded-3 p-3 text-center" style="width: 60px; height: 60px;">
                <i class="fa-solid fa-building fa-2x"></i>
            </div>
            <div>
                <div class="d-flex align-items-center gap-2">
                    <h4 class="fw-bold mb-0">{{ $client->company_name }}</h4>
                    <span class="badge bg-light text-primary border">{{ $client->client_id }}</span>
                    <span class="badge {{ $client->status === 'active' ? 'bg-success-subtle text-success' : 'bg-secondary-subtle text-secondary' }}">
                        {{ ucfirst($client->status) }}
                    </span>
                </div>
                <div class="text-muted fs-14 mt-1">
                    <i class="fa-solid fa-user me-1"></i> {{ $client->contact_person ?: 'No Contact Person' }} &bull;
                    <i class="fa-solid fa-envelope me-1"></i> {{ $client->email }} &bull;
                    <i class="fa-solid fa-phone me-1"></i> {{ $client->phone ?: 'N/A' }}
                </div>
            </div>
        </div>

        <div class="d-flex gap-2">
            <a href="{{ route('invoices.create', ['client_id' => $client->id]) }}" class="btn btn-primary">
                <i class="fa-solid fa-plus me-1"></i> Create Invoice
            </a>
            <a href="{{ route('clients.statement', $client) }}" class="btn btn-outline-secondary">
                <i class="fa-solid fa-file-contract me-1"></i> Statement
            </a>
            <a href="{{ route('clients.edit', $client) }}" class="btn btn-light border">
                <i class="fa-regular fa-pen-to-square"></i>
            </a>
        </div>
    </div>
</div>

<!-- Financial Summary Cards -->
<div class="row g-3 mb-4">
    <div class="col-6 col-md-4 col-xl-2">
        <div class="stat-card">
            <small class="text-muted fs-12 fw-medium d-block mb-1">Total Invoices</small>
            <h5 class="fw-bold mb-0">{{ $client->invoices->count() }}</h5>
        </div>
    </div>
    <div class="col-6 col-md-4 col-xl-2">
        <div class="stat-card">
            <small class="text-muted fs-12 fw-medium d-block mb-1">Total Invoiced</small>
            <h5 class="fw-bold mb-0 text-primary">${{ number_format($client->total_invoiced, 2) }}</h5>
        </div>
    </div>
    <div class="col-6 col-md-4 col-xl-2">
        <div class="stat-card">
            <small class="text-muted fs-12 fw-medium d-block mb-1">Total Paid</small>
            <h5 class="fw-bold mb-0 text-success">${{ number_format($client->total_paid, 2) }}</h5>
        </div>
    </div>
    <div class="col-6 col-md-4 col-xl-2">
        <div class="stat-card">
            <small class="text-muted fs-12 fw-medium d-block mb-1">Outstanding</small>
            <h5 class="fw-bold mb-0 text-warning">${{ number_format($client->total_outstanding, 2) }}</h5>
        </div>
    </div>
    <div class="col-6 col-md-4 col-xl-2">
        <div class="stat-card">
            <small class="text-muted fs-12 fw-medium d-block mb-1">Overdue Amount</small>
            <h5 class="fw-bold mb-0 text-danger">${{ number_format($client->total_overdue, 2) }}</h5>
        </div>
    </div>
    <div class="col-6 col-md-4 col-xl-2">
        <div class="stat-card">
            <small class="text-muted fs-12 fw-medium d-block mb-1">Currency / Terms</small>
            <h6 class="fw-bold mb-0 text-dark">{{ $client->currency }} &bull; {{ $client->payment_terms }}</h6>
        </div>
    </div>
</div>

<!-- Outstanding Invoice Alert -->
@if($client->total_outstanding > 0)
<div class="alert alert-warning border-warning border-start border-4 shadow-sm mb-4">
    <div class="d-flex align-items-center justify-content-between">
        <div>
            <h6 class="fw-bold mb-1"><i class="fa-solid fa-triangle-exclamation me-2"></i> Outstanding Balance Alert</h6>
            <span class="fs-14">This client currently has an outstanding balance of <strong>${{ number_format($client->total_outstanding, 2) }}</strong>.</span>
        </div>
        <a href="{{ route('payments.create', ['client_id' => $client->id]) }}" class="btn btn-sm btn-warning text-dark fw-bold">
            Record Payment
        </a>
    </div>
</div>
@endif

<!-- Tabs: Invoices, Payments, Projects, Timeline -->
<ul class="nav nav-tabs nav-tabs-custom mb-3" id="clientTabs" role="tablist">
    <li class="nav-item">
        <button class="nav-link active fw-semibold" id="invoices-tab" data-bs-toggle="tab" data-bs-target="#invoices" type="button">
            <i class="fa-solid fa-file-invoice me-1"></i> Invoice History ({{ $client->invoices->count() }})
        </button>
    </li>
    <li class="nav-item">
        <button class="nav-link fw-semibold" id="payments-tab" data-bs-toggle="tab" data-bs-target="#payments" type="button">
            <i class="fa-solid fa-credit-card me-1"></i> Payment Ledger ({{ $client->payments->count() }})
        </button>
    </li>
    <li class="nav-item">
        <button class="nav-link fw-semibold" id="projects-tab" data-bs-toggle="tab" data-bs-target="#projects" type="button">
            <i class="fa-solid fa-diagram-project me-1"></i> Projects ({{ $projects->count() }})
        </button>
    </li>
    <li class="nav-item">
        <button class="nav-link fw-semibold" id="details-tab" data-bs-toggle="tab" data-bs-target="#details" type="button">
            <i class="fa-solid fa-info-circle me-1"></i> Complete Profile Details
        </button>
    </li>
</ul>

<div class="tab-content" id="clientTabsContent">
    <!-- Invoice History Tab -->
    <div class="tab-pane fade show active" id="invoices">
        <div class="card-custom">
            <div class="table-responsive">
                <table class="table table-custom align-middle">
                    <thead>
                        <tr>
                            <th>Invoice #</th>
                            <th>Date</th>
                            <th>Due Date</th>
                            <th>Amount</th>
                            <th>Paid</th>
                            <th>Balance</th>
                            <th>Status</th>
                            <th class="text-end">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($invoices as $inv)
                        <tr>
                            <td class="fw-bold">
                                <a href="{{ route('invoices.show', $inv) }}" class="text-decoration-none text-dark">{{ $inv->invoice_number }}</a>
                            </td>
                            <td>{{ $inv->invoice_date->format('M d, Y') }}</td>
                            <td>{{ $inv->due_date->format('M d, Y') }}</td>
                            <td class="fw-bold">{{ $inv->currency_symbol }}{{ number_format($inv->grand_total, 2) }}</td>
                            <td class="text-success">{{ $inv->currency_symbol }}{{ number_format($inv->paid_amount, 2) }}</td>
                            <td class="text-danger fw-semibold">{{ $inv->currency_symbol }}{{ number_format($inv->balance_due, 2) }}</td>
                            <td>
                                <span class="badge badge-status badge-{{ strtolower(str_replace(' ', '', $inv->status)) }}">
                                    {{ $inv->status }}
                                </span>
                            </td>
                            <td class="text-end">
                                <div class="btn-group">
                                    <a href="{{ route('invoices.show', $inv) }}" class="btn btn-sm btn-light border" title="View"><i class="fa-regular fa-eye"></i></a>
                                    <a href="{{ route('invoices.download_pdf', $inv) }}" class="btn btn-sm btn-light border" title="Download PDF"><i class="fa-solid fa-file-pdf text-danger"></i></a>
                                    @if($inv->balance_due > 0 && $inv->status !== 'Cancelled')
                                    <a href="{{ route('payments.create', ['invoice_id' => $inv->id]) }}" class="btn btn-sm btn-light border text-success" title="Record Payment"><i class="fa-solid fa-hand-holding-dollar"></i></a>
                                    @endif
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="8" class="text-center text-muted py-4">No invoices recorded for this client.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="p-3 border-top">
                {{ $invoices->links() }}
            </div>
        </div>
    </div>

    <!-- Payments Tab -->
    <div class="tab-pane fade" id="payments">
        <div class="card-custom">
            <div class="table-responsive">
                <table class="table table-custom align-middle">
                    <thead>
                        <tr>
                            <th>Payment ID</th>
                            <th>Invoice Number</th>
                            <th>Date</th>
                            <th>Method</th>
                            <th>Transaction ID</th>
                            <th>Amount</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($payments as $pay)
                        <tr>
                            <td class="fw-bold">{{ $pay->payment_id }}</td>
                            <td>
                                @if($pay->invoice)
                                <a href="{{ route('invoices.show', $pay->invoice) }}" class="text-decoration-none">{{ $pay->invoice->invoice_number }}</a>
                                @else N/A @endif
                            </td>
                            <td>{{ $pay->payment_date->format('M d, Y') }}</td>
                            <td><span class="badge bg-light text-dark border">{{ $pay->payment_method }}</span></td>
                            <td><code>{{ $pay->transaction_id ?: 'N/A' }}</code></td>
                            <td class="fw-bold text-success">+${{ number_format($pay->amount, 2) }}</td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="text-center text-muted py-4">No payments recorded for this client yet.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Projects Tab -->
    <div class="tab-pane fade" id="projects">
        <div class="card-custom">
            <div class="p-3 border-bottom d-flex align-items-center justify-content-between">
                <h6 class="fw-bold mb-0">Client Projects</h6>
                <a href="{{ route('projects.create', ['client_id' => $client->id]) }}" class="btn btn-sm btn-primary">
                    <i class="fa-solid fa-plus me-1"></i> New Project
                </a>
            </div>
            <div class="table-responsive">
                <table class="table table-custom align-middle">
                    <thead>
                        <tr>
                            <th>Project ID</th>
                            <th>Project Name</th>
                            <th>Status</th>
                            <th>Budget</th>
                            <th>Dates</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($projects as $prj)
                        <tr>
                            <td class="fw-bold">{{ $prj->project_id }}</td>
                            <td class="fw-semibold">{{ $prj->project_name }}</td>
                            <td><span class="badge bg-info-subtle text-info">{{ $prj->status }}</span></td>
                            <td class="fw-bold">${{ number_format($prj->budget, 2) }}</td>
                            <td class="fs-13 text-muted">
                                {{ $prj->start_date ? \Carbon\Carbon::parse($prj->start_date)->format('M Y') : 'N/A' }} - 
                                {{ $prj->end_date ? \Carbon\Carbon::parse($prj->end_date)->format('M Y') : 'Ongoing' }}
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="text-center text-muted py-4">No projects associated with this client.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Full Details Tab -->
    <div class="tab-pane fade" id="details">
        <div class="row g-4">
            <div class="col-md-6">
                <div class="card-custom p-4">
                    <h6 class="fw-bold border-bottom pb-2 mb-3">Client Information</h6>
                    <table class="table table-borderless fs-14 mb-0">
                        <tr><td class="text-muted w-35">Company Name:</td><td class="fw-semibold">{{ $client->company_name }}</td></tr>
                        <tr><td class="text-muted">Contact Person:</td><td>{{ $client->contact_person ?: 'N/A' }}</td></tr>
                        <tr><td class="text-muted">Primary Email:</td><td>{{ $client->email }}</td></tr>
                        <tr><td class="text-muted">Secondary Email:</td><td>{{ $client->secondary_email ?: 'N/A' }}</td></tr>
                        <tr><td class="text-muted">Phone:</td><td>{{ $client->phone ?: 'N/A' }}</td></tr>
                        <tr><td class="text-muted">WhatsApp:</td><td>{{ $client->whatsapp ?: 'N/A' }}</td></tr>
                        <tr><td class="text-muted">Website:</td><td><a href="{{ $client->website }}" target="_blank">{{ $client->website ?: 'N/A' }}</a></td></tr>
                    </table>
                </div>
            </div>

            <div class="col-md-6">
                <div class="card-custom p-4">
                    <h6 class="fw-bold border-bottom pb-2 mb-3">Address & Tax Information</h6>
                    <table class="table table-borderless fs-14 mb-0">
                        <tr><td class="text-muted w-35">Address:</td><td>{{ $client->address ?: 'N/A' }}</td></tr>
                        <tr><td class="text-muted">City / State:</td><td>{{ $client->city }}, {{ $client->state }} {{ $client->zip_code }}</td></tr>
                        <tr><td class="text-muted">Country:</td><td>{{ $client->country }}</td></tr>
                        <tr><td class="text-muted">Tax/VAT Number:</td><td><code>{{ $client->tax_number ?: 'N/A' }}</code></td></tr>
                        <tr><td class="text-muted">Registration #:</td><td><code>{{ $client->registration_number ?: 'N/A' }}</code></td></tr>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
