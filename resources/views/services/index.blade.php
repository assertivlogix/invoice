@extends('layouts.app')

@section('title', 'Services Catalog')
@section('page-title', 'Services & Products Catalog')

@section('content')
<div class="d-flex align-items-center justify-content-between mb-4">
    <div>
        <h5 class="fw-bold mb-1">Services Catalog</h5>
        <small class="text-muted">Master product & service list for quick invoice line-item selection</small>
    </div>
    <a href="{{ route('services.create') }}" class="btn btn-primary">
        <i class="fa-solid fa-plus me-1"></i> Add New Service
    </a>
</div>

<div class="card-custom p-3 mb-4">
    <form action="{{ route('services.index') }}" method="GET" class="row g-3">
        <div class="col-md-7">
            <input type="text" name="search" class="form-control" placeholder="Search service name or SKU..." value="{{ request('search') }}">
        </div>
        <div class="col-md-3">
            <select name="status" class="form-select">
                <option value="">-- All Statuses --</option>
                <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Active</option>
                <option value="inactive" {{ request('status') === 'inactive' ? 'selected' : '' }}>Inactive</option>
            </select>
        </div>
        <div class="col-md-2 d-flex gap-2">
            <button type="submit" class="btn btn-primary w-100">Filter</button>
            <a href="{{ route('services.index') }}" class="btn btn-light border"><i class="fa-solid fa-rotate-right"></i></a>
        </div>
    </form>
</div>

<div class="card-custom">
    <div class="table-responsive">
        <table class="table table-custom align-middle">
            <thead>
                <tr>
                    <th>SKU</th>
                    <th>Service Name</th>
                    <th>Unit</th>
                    <th>Default Price</th>
                    <th>Tax Rate</th>
                    <th>Status</th>
                    <th class="text-end">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($services as $svc)
                <tr>
                    <td class="fw-bold text-muted"><code>{{ $svc->sku ?: 'N/A' }}</code></td>
                    <td class="fw-semibold">
                        <div class="text-dark">{{ $svc->service_name }}</div>
                        <small class="text-muted fs-12">{{ Str::limit($svc->description, 60) }}</small>
                    </td>
                    <td><span class="badge bg-light text-dark border">{{ $svc->unit }}</span></td>
                    <td class="fw-bold text-primary">${{ number_format($svc->default_price, 2) }}</td>
                    <td>{{ $svc->tax_rate }}%</td>
                    <td>
                        <span class="badge {{ $svc->status === 'active' ? 'bg-success-subtle text-success' : 'bg-secondary-subtle text-secondary' }} px-2 py-1">
                            {{ ucfirst($svc->status) }}
                        </span>
                    </td>
                    <td class="text-end">
                        <a href="{{ route('services.edit', $svc) }}" class="btn btn-sm btn-light border" title="Edit"><i class="fa-regular fa-pen-to-square"></i></a>
                        <form action="{{ route('services.destroy', $svc) }}" method="POST" class="d-inline" onsubmit="return confirm('Delete service {{ $svc->service_name }}?');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-sm btn-light border text-danger" title="Delete"><i class="fa-regular fa-trash-can"></i></button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" class="text-center text-muted py-4">No services registered in catalog.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="p-3 border-top">
        {{ $services->links() }}
    </div>
</div>
@endsection
