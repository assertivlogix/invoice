@extends('layouts.app')

@section('title', 'Edit Service')
@section('page-title', 'Edit Service Item')

@section('content')
<div class="mb-4">
    <a href="{{ route('services.index') }}" class="text-decoration-none text-muted"><i class="fa-solid fa-arrow-left me-1"></i> Back to Services</a>
    <h4 class="fw-bold mt-2">Edit {{ $service->service_name }}</h4>
</div>

<div class="row justify-content-center">
    <div class="col-12 col-lg-8">
        <div class="card-custom p-4">
            <form action="{{ route('services.update', $service) }}" method="POST">
                @csrf
                @method('PUT')
                <div class="row g-3">
                    <div class="col-md-8">
                        <label class="form-label fs-14 fw-medium">Service Name <span class="text-danger">*</span></label>
                        <input type="text" name="service_name" class="form-control" value="{{ old('service_name', $service->service_name) }}" required>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fs-14 fw-medium">SKU / Code</label>
                        <input type="text" name="sku" class="form-control" value="{{ old('sku', $service->sku) }}">
                    </div>

                    <div class="col-md-4">
                        <label class="form-label fs-14 fw-medium">Billing Unit <span class="text-danger">*</span></label>
                        <select name="unit" class="form-select" required>
                            <option value="Hour" {{ old('unit', $service->unit) === 'Hour' ? 'selected' : '' }}>Hour</option>
                            <option value="Day" {{ old('unit', $service->unit) === 'Day' ? 'selected' : '' }}>Day</option>
                            <option value="Project" {{ old('unit', $service->unit) === 'Project' ? 'selected' : '' }}>Project</option>
                            <option value="Month" {{ old('unit', $service->unit) === 'Month' ? 'selected' : '' }}>Month</option>
                            <option value="License" {{ old('unit', $service->unit) === 'License' ? 'selected' : '' }}>License</option>
                            <option value="Item" {{ old('unit', $service->unit) === 'Item' ? 'selected' : '' }}>Item</option>
                        </select>
                    </div>

                    <div class="col-md-4">
                        <label class="form-label fs-14 fw-medium">Default Unit Price ($) <span class="text-danger">*</span></label>
                        <input type="number" step="0.01" name="default_price" class="form-control" value="{{ old('default_price', $service->default_price) }}" required>
                    </div>

                    <div class="col-md-4">
                        <label class="form-label fs-14 fw-medium">Default Tax Rate (%)</label>
                        <input type="number" step="0.01" name="tax_rate" class="form-control" value="{{ old('tax_rate', $service->tax_rate) }}">
                    </div>

                    <div class="col-md-6">
                        <label class="form-label fs-14 fw-medium">Status <span class="text-danger">*</span></label>
                        <select name="status" class="form-select" required>
                            <option value="active" {{ old('status', $service->status) === 'active' ? 'selected' : '' }}>Active</option>
                            <option value="inactive" {{ old('status', $service->status) === 'inactive' ? 'selected' : '' }}>Inactive</option>
                        </select>
                    </div>

                    <div class="col-12">
                        <label class="form-label fs-14 fw-medium">Description</label>
                        <textarea name="description" class="form-control" rows="3">{{ old('description', $service->description) }}</textarea>
                    </div>

                    <div class="col-12 mt-4">
                        <button type="submit" class="btn btn-primary px-4 fw-semibold"><i class="fa-solid fa-floppy-disk me-1"></i> Update Service</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
