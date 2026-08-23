@extends('layouts.app')

@section('title', 'Add New Project')
@section('page-title', 'Create Project')

@section('content')
<div class="mb-4">
    <a href="{{ route('projects.index') }}" class="text-decoration-none text-muted"><i class="fa-solid fa-arrow-left me-1"></i> Back to Projects</a>
    <h4 class="fw-bold mt-2">New Project</h4>
</div>

<div class="row justify-content-center">
    <div class="col-12 col-lg-8">
        <div class="card-custom p-4">
            <form action="{{ route('projects.store') }}" method="POST">
                @csrf
                <div class="row g-3">
                    <div class="col-md-4">
                        <label class="form-label fs-14 fw-medium">Project ID <span class="text-danger">*</span></label>
                        <input type="text" name="project_id" class="form-control" value="{{ old('project_id', $suggestedId) }}" required>
                    </div>
                    <div class="col-md-8">
                        <label class="form-label fs-14 fw-medium">Client <span class="text-danger">*</span></label>
                        <select name="client_id" class="form-select" required>
                            <option value="">-- Select Client --</option>
                            @foreach($clients as $c)
                                <option value="{{ $c->id }}" {{ old('client_id') == $c->id ? 'selected' : '' }}>{{ $c->company_name }} ({{ $c->client_id }})</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-12">
                        <label class="form-label fs-14 fw-medium">Project Name <span class="text-danger">*</span></label>
                        <input type="text" name="project_name" class="form-control" value="{{ old('project_name') }}" placeholder="e.g. WooCommerce E-Commerce Portal" required>
                    </div>

                    <div class="col-md-4">
                        <label class="form-label fs-14 fw-medium">Budget ($) <span class="text-danger">*</span></label>
                        <input type="number" step="0.01" name="budget" class="form-control" value="{{ old('budget', '0.00') }}" required>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fs-14 fw-medium">Start Date</label>
                        <input type="date" name="start_date" class="form-control" value="{{ old('start_date') }}">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fs-14 fw-medium">End Date</label>
                        <input type="date" name="end_date" class="form-control" value="{{ old('end_date') }}">
                    </div>

                    <div class="col-md-6">
                        <label class="form-label fs-14 fw-medium">Project Status <span class="text-danger">*</span></label>
                        <select name="status" class="form-select" required>
                            <option value="Planning" {{ old('status') === 'Planning' ? 'selected' : '' }}>Planning</option>
                            <option value="In Progress" {{ old('status', 'In Progress') === 'In Progress' ? 'selected' : '' }}>In Progress</option>
                            <option value="On Hold" {{ old('status') === 'On Hold' ? 'selected' : '' }}>On Hold</option>
                            <option value="Completed" {{ old('status') === 'Completed' ? 'selected' : '' }}>Completed</option>
                            <option value="Cancelled" {{ old('status') === 'Cancelled' ? 'selected' : '' }}>Cancelled</option>
                        </select>
                    </div>

                    <div class="col-12">
                        <label class="form-label fs-14 fw-medium">Description</label>
                        <textarea name="description" class="form-control" rows="3" placeholder="Project scope, deliverables..."></textarea>
                    </div>

                    <div class="col-12 mt-4">
                        <button type="submit" class="btn btn-primary px-4 fw-semibold"><i class="fa-solid fa-check me-1"></i> Save Project</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
