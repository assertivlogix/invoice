@extends('layouts.app')

@section('title', 'Clients')
@section('page-title', 'Client Management')

@section('content')
<div class="d-flex align-items-center justify-content-between mb-4">
    <div>
        <h5 class="fw-bold mb-1">All Clients</h5>
        <small class="text-muted">Manage your client relationships and accounts</small>
    </div>
    <a href="{{ route('clients.create') }}" class="btn btn-primary">
        <i class="fa-solid fa-plus me-1"></i> Add New Client
    </a>
</div>

<!-- Filters & Search -->
<div class="card-custom p-3 mb-4">
    <form action="{{ route('clients.index') }}" method="GET" class="row g-3">
        <div class="col-12 col-md-5">
            <div class="input-group">
                <span class="input-group-text bg-white text-muted"><i class="fa-solid fa-magnifying-glass"></i></span>
                <input type="text" name="search" class="form-control" placeholder="Search by name, ID, or email..." value="{{ request('search') }}">
            </div>
        </div>
        <div class="col-12 col-md-3">
            <select name="status" class="form-select">
                <option value="">-- All Statuses --</option>
                <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Active</option>
                <option value="inactive" {{ request('status') === 'inactive' ? 'selected' : '' }}>Inactive</option>
            </select>
        </div>
        <div class="col-12 col-md-2">
            <select name="country" class="form-select">
                <option value="">-- All Countries --</option>
                @foreach($countries as $country)
                    <option value="{{ $country }}" {{ request('country') === $country ? 'selected' : '' }}>{{ $country }}</option>
                @endforeach
            </select>
        </div>
        <div class="col-12 col-md-2 d-flex gap-2">
            <button type="submit" class="btn btn-primary w-100">Filter</button>
            <a href="{{ route('clients.index') }}" class="btn btn-light border"><i class="fa-solid fa-rotate-right"></i></a>
        </div>
    </form>
</div>

<!-- Clients Table -->
<div class="card-custom">
    <div class="table-responsive">
        <table class="table table-custom align-middle">
            <thead>
                <tr>
                    <th>Client ID</th>
                    <th>Company Name</th>
                    <th>Contact Person</th>
                    <th>Email & Phone</th>
                    <th>Country</th>
                    <th>Total Invoiced</th>
                    <th>Paid</th>
                    <th>Outstanding</th>
                    <th>Status</th>
                    <th class="text-end">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($clients as $client)
                <tr>
                    <td class="fw-bold text-primary">{{ $client->client_id }}</td>
                    <td class="fw-semibold">
                        <a href="{{ route('clients.show', $client) }}" class="text-decoration-none text-dark">{{ $client->company_name }}</a>
                    </td>
                    <td>{{ $client->contact_person ?: 'N/A' }}</td>
                    <td>
                        <div class="fs-13">{{ $client->email }}</div>
                        <small class="text-muted fs-12">{{ $client->phone }}</small>
                    </td>
                    <td>{{ $client->country ?: 'N/A' }}</td>
                    <td class="fw-semibold">${{ number_format($client->total_invoiced, 2) }}</td>
                    <td class="text-success fw-semibold">${{ number_format($client->total_paid, 2) }}</td>
                    <td class="text-danger fw-semibold">${{ number_format($client->total_outstanding, 2) }}</td>
                    <td>
                        <span class="badge {{ $client->status === 'active' ? 'bg-success-subtle text-success' : 'bg-secondary-subtle text-secondary' }} px-2 py-1">
                            {{ ucfirst($client->status) }}
                        </span>
                    </td>
                    <td class="text-end">
                        <div class="btn-group">
                            <a href="{{ route('clients.show', $client) }}" class="btn btn-sm btn-light border" title="View Dashboard"><i class="fa-regular fa-eye"></i></a>
                            <a href="{{ route('clients.edit', $client) }}" class="btn btn-sm btn-light border" title="Edit"><i class="fa-regular fa-pen-to-square"></i></a>
                            @if(auth()->user()->isAdmin())
                            <form action="{{ route('clients.destroy', $client) }}" method="POST" class="d-inline" onsubmit="return confirm('Delete client {{ $client->company_name }}?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-light border text-danger" title="Delete"><i class="fa-regular fa-trash-can"></i></button>
                            </form>
                            @endif
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="10" class="text-center text-muted py-4">No clients found matching criteria.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="p-3 border-top">
        {{ $clients->links() }}
    </div>
</div>
@endsection
