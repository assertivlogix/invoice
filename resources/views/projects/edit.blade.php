@extends('layouts.app')

@section('title', 'Edit Project')
@section('page-title', 'Edit Project')

@section('content')
<div class="mb-4">
    <a href="{{ route('projects.index') }}" class="text-decoration-none text-muted"><i class="fa-solid fa-arrow-left me-1"></i> Back to Projects</a>
    <h4 class="fw-bold mt-2">Edit Project: {{ $project->project_name }}</h4>
</div>

<div class="row justify-content-center">
    <div class="col-12 col-lg-8">
        <div class="card-custom p-4">
            <form action="{{ route('projects.update', $project) }}" method="POST">
                @csrf
                @method('PUT')
                <div class="row g-3">
                    <div class="col-md-4">
                        <label class="form-label fs-14 fw-medium">Project ID</label>
                        <input type="text" class="form-control bg-light" value="{{ $project->project_id }}" disabled>
                    </div>
                    <div class="col-md-8">
                        <label class="form-label fs-14 fw-medium">Client <span class="text-danger">*</span></label>
                        <select name="client_id" class="form-select" required>
                            @foreach($clients as $c)
                                <option value="{{ $c->id }}" {{ old('client_id', $project->client_id) == $c->id ? 'selected' : '' }}>{{ $c->company_name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-12">
                        <label class="form-label fs-14 fw-medium">Project Name <span class="text-danger">*</span></label>
                        <input type="text" name="project_name" class="form-control" value="{{ old('project_name', $project->project_name) }}" required>
                    </div>

                    <div class="col-md-4">
                        <label class="form-label fs-14 fw-medium">Budget ($) <span class="text-danger">*</span></label>
                        <input type="number" step="0.01" name="budget" class="form-control" value="{{ old('budget', $project->budget) }}" required>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fs-14 fw-medium">Start Date</label>
                        <input type="date" name="start_date" class="form-control" value="{{ old('start_date', $project->start_date) }}">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fs-14 fw-medium">End Date</label>
                        <input type="date" name="end_date" class="form-control" value="{{ old('end_date', $project->end_date) }}">
                    </div>

                    <div class="col-md-6">
                        <label class="form-label fs-14 fw-medium">Project Status <span class="text-danger">*</span></label>
                        <select name="status" class="form-select" required>
                            <option value="Planning" {{ old('status', $project->status) === 'Planning' ? 'selected' : '' }}>Planning</option>
                            <option value="In Progress" {{ old('status', $project->status) === 'In Progress' ? 'selected' : '' }}>In Progress</option>
                            <option value="On Hold" {{ old('status', $project->status) === 'On Hold' ? 'selected' : '' }}>On Hold</option>
                            <option value="Completed" {{ old('status', $project->status) === 'Completed' ? 'selected' : '' }}>Completed</option>
                            <option value="Cancelled" {{ old('status', $project->status) === 'Cancelled' ? 'selected' : '' }}>Cancelled</option>
                        </select>
                    </div>

                    <div class="col-12">
                        <label class="form-label fs-14 fw-medium">Description</label>
                        <textarea name="description" class="form-control" rows="3">{{ old('description', $project->description) }}</textarea>
                    </div>

                    <div class="col-12 mt-4">
                        <button type="submit" class="btn btn-primary px-4 fw-semibold"><i class="fa-solid fa-floppy-disk me-1"></i> Update Project</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
