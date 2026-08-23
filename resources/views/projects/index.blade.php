@extends('layouts.app')

@section('title', 'Projects')
@section('page-title', 'Project Management')

@section('content')
<div class="d-flex align-items-center justify-content-between mb-4">
    <div>
        <h5 class="fw-bold mb-1">All Projects</h5>
        <small class="text-muted">Track projects provided to clients</small>
    </div>
    <a href="{{ route('projects.create') }}" class="btn btn-primary">
        <i class="fa-solid fa-plus me-1"></i> Add New Project
    </a>
</div>

<div class="card-custom p-3 mb-4">
    <form action="{{ route('projects.index') }}" method="GET" class="row g-3">
        <div class="col-md-5">
            <input type="text" name="search" class="form-control" placeholder="Search by project name or ID..." value="{{ request('search') }}">
        </div>
        <div class="col-md-3">
            <select name="client_id" class="form-select">
                <option value="">-- All Clients --</option>
                @foreach($clients as $c)
                    <option value="{{ $c->id }}" {{ request('client_id') == $c->id ? 'selected' : '' }}>{{ $c->company_name }}</option>
                @endforeach
            </select>
        </div>
        <div class="col-md-2">
            <select name="status" class="form-select">
                <option value="">-- All Statuses --</option>
                <option value="Planning" {{ request('status') === 'Planning' ? 'selected' : '' }}>Planning</option>
                <option value="In Progress" {{ request('status') === 'In Progress' ? 'selected' : '' }}>In Progress</option>
                <option value="On Hold" {{ request('status') === 'On Hold' ? 'selected' : '' }}>On Hold</option>
                <option value="Completed" {{ request('status') === 'Completed' ? 'selected' : '' }}>Completed</option>
                <option value="Cancelled" {{ request('status') === 'Cancelled' ? 'selected' : '' }}>Cancelled</option>
            </select>
        </div>
        <div class="col-md-2 d-flex gap-2">
            <button type="submit" class="btn btn-primary w-100">Filter</button>
            <a href="{{ route('projects.index') }}" class="btn btn-light border"><i class="fa-solid fa-rotate-right"></i></a>
        </div>
    </form>
</div>

<div class="card-custom">
    <div class="table-responsive">
        <table class="table table-custom align-middle">
            <thead>
                <tr>
                    <th>Project ID</th>
                    <th>Project Name</th>
                    <th>Client</th>
                    <th>Budget</th>
                    <th>Dates</th>
                    <th>Status</th>
                    <th class="text-end">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($projects as $prj)
                <tr>
                    <td class="fw-bold text-primary">{{ $prj->project_id }}</td>
                    <td class="fw-semibold">{{ $prj->project_name }}</td>
                    <td><a href="{{ route('clients.show', $prj->client) }}" class="text-decoration-none text-dark">{{ $prj->client->company_name }}</a></td>
                    <td class="fw-bold">${{ number_format($prj->budget, 2) }}</td>
                    <td class="fs-13 text-muted">
                        {{ $prj->start_date ? \Carbon\Carbon::parse($prj->start_date)->format('M d, Y') : 'N/A' }} - 
                        {{ $prj->end_date ? \Carbon\Carbon::parse($prj->end_date)->format('M d, Y') : 'Ongoing' }}
                    </td>
                    <td>
                        <span class="badge bg-info-subtle text-info px-2 py-1">{{ $prj->status }}</span>
                    </td>
                    <td class="text-end">
                        <a href="{{ route('projects.edit', $prj) }}" class="btn btn-sm btn-light border" title="Edit"><i class="fa-regular fa-pen-to-square"></i></a>
                        <form action="{{ route('projects.destroy', $prj) }}" method="POST" class="d-inline" onsubmit="return confirm('Delete project?');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-sm btn-light border text-danger" title="Delete"><i class="fa-regular fa-trash-can"></i></button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" class="text-center text-muted py-4">No projects found.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="p-3 border-top">
        {{ $projects->links() }}
    </div>
</div>
@endsection
