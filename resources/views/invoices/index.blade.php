@extends('layouts.app')

@section('title', 'Invoices')
@section('page-title', 'Invoice Management')

@section('content')
<div class="d-flex align-items-center justify-content-between mb-4">
    <div>
        <h5 class="fw-bold mb-1">All Invoices</h5>
        <small class="text-muted">Generate, send, and track client invoices</small>
    </div>
    <a href="{{ route('invoices.create') }}" class="btn btn-primary">
        <i class="fa-solid fa-plus me-1"></i> Create Invoice
    </a>
</div>

<!-- Filters Bar -->
<div class="card-custom p-3 mb-4">
    <form action="{{ route('invoices.index') }}" method="GET" class="row g-3">
        <div class="col-12 col-md-4">
            <input type="text" name="search" class="form-control" placeholder="Search by invoice #, reference, client..." value="{{ request('search') }}">
        </div>
        <div class="col-12 col-md-2">
            <select name="client_id" class="form-select">
                <option value="">-- All Clients --</option>
                @foreach($clients as $c)
                    <option value="{{ $c->id }}" {{ request('client_id') == $c->id ? 'selected' : '' }}>{{ $c->company_name }}</option>
                @endforeach
            </select>
        </div>
        <div class="col-12 col-md-2">
            <select name="status" class="form-select">
                <option value="">-- All Statuses --</option>
                <option value="Draft" {{ request('status') === 'Draft' ? 'selected' : '' }}>Draft</option>
                <option value="Sent" {{ request('status') === 'Sent' ? 'selected' : '' }}>Sent</option>
                <option value="Partially Paid" {{ request('status') === 'Partially Paid' ? 'selected' : '' }}>Partially Paid</option>
                <option value="Paid" {{ request('status') === 'Paid' ? 'selected' : '' }}>Paid</option>
                <option value="Overdue" {{ request('status') === 'Overdue' ? 'selected' : '' }}>Overdue</option>
                <option value="Cancelled" {{ request('status') === 'Cancelled' ? 'selected' : '' }}>Cancelled</option>
            </select>
        </div>
        <div class="col-12 col-md-2">
            <select name="sort" class="form-select">
                <option value="newest" {{ request('sort') === 'newest' ? 'selected' : '' }}>Newest First</option>
                <option value="oldest" {{ request('sort') === 'oldest' ? 'selected' : '' }}>Oldest First</option>
                <option value="highest" {{ request('sort') === 'highest' ? 'selected' : '' }}>Highest Amount</option>
                <option value="lowest" {{ request('sort') === 'lowest' ? 'selected' : '' }}>Lowest Amount</option>
                <option value="due_date" {{ request('sort') === 'due_date' ? 'selected' : '' }}>Due Date</option>
            </select>
        </div>
        <div class="col-12 col-md-2 d-flex gap-2">
            <button type="submit" class="btn btn-primary w-100">Filter</button>
            <a href="{{ route('invoices.index') }}" class="btn btn-light border"><i class="fa-solid fa-rotate-right"></i></a>
        </div>
    </form>
</div>

<!-- Invoices Table -->
<div class="card-custom">
    <div class="table-responsive">
        <table class="table table-custom align-middle">
            <thead>
                <tr>
                    <th>Invoice #</th>
                    <th>Client</th>
                    <th>Date</th>
                    <th>Due Date</th>
                    <th>Total</th>
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
                    <td class="fw-semibold">
                        <a href="{{ route('clients.show', $inv->client) }}" class="text-decoration-none text-dark">{{ $inv->client->company_name }}</a>
                        @if($inv->project)
                            <div class="fs-12 text-muted"><i class="fa-solid fa-diagram-project me-1"></i> {{ $inv->project->project_name }}</div>
                        @endif
                    </td>
                    <td>{{ $inv->invoice_date->format('M d, Y') }}</td>
                    <td>{{ $inv->due_date->format('M d, Y') }}</td>
                    <td class="fw-bold">{{ $inv->currency_symbol }}{{ number_format($inv->grand_total, 2) }}</td>
                    <td class="text-success font-monospace">{{ $inv->currency_symbol }}{{ number_format($inv->paid_amount, 2) }}</td>
                    <td class="text-danger font-monospace fw-semibold">{{ $inv->currency_symbol }}{{ number_format($inv->balance_due, 2) }}</td>
                    <td>
                        <span class="badge badge-status badge-{{ strtolower(str_replace(' ', '', $inv->status)) }}">
                            {{ $inv->status }}
                        </span>
                    </td>
                    <td class="text-end">
                        <div class="btn-group">
                            <a href="{{ route('invoices.show', $inv) }}" class="btn btn-sm btn-light border" title="View Preview"><i class="fa-regular fa-eye"></i></a>
                            <a href="{{ route('invoices.edit', $inv) }}" class="btn btn-sm btn-light border" title="Edit"><i class="fa-regular fa-pen-to-square"></i></a>
                            <a href="{{ route('invoices.download_pdf', $inv) }}" class="btn btn-sm btn-light border" title="Download PDF"><i class="fa-solid fa-file-pdf text-danger"></i></a>
                            <form action="{{ route('invoices.duplicate', $inv) }}" method="POST" class="d-inline">
                                @csrf
                                <button type="submit" class="btn btn-sm btn-light border" title="Duplicate"><i class="fa-regular fa-copy"></i></button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="9" class="text-center text-muted py-4">No invoices found matching criteria.</td>
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
