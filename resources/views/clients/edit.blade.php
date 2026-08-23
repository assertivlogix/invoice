@extends('layouts.app')

@section('title', 'Edit Client — ' . $client->company_name)
@section('page-title', 'Edit Client Profile')

@section('content')
<div class="mb-4">
    <a href="{{ route('clients.show', $client) }}" class="text-decoration-none text-muted"><i class="fa-solid fa-arrow-left me-1"></i> Back to Client Profile</a>
    <h4 class="fw-bold mt-2">Edit {{ $client->company_name }}</h4>
</div>

<form action="{{ route('clients.update', $client) }}" method="POST">
    @csrf
    @method('PUT')
    <div class="row g-4">
        <div class="col-12 col-lg-8">
            <div class="card-custom p-4 mb-4">
                <h6 class="fw-bold border-bottom pb-2 mb-3"><i class="fa-solid fa-building me-2 text-primary"></i> Company & Contact Information</h6>
                
                <div class="row g-3">
                    <div class="col-md-4">
                        <label class="form-label fs-14 fw-medium">Client ID</label>
                        <input type="text" class="form-control bg-light" value="{{ $client->client_id }}" disabled>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fs-14 fw-medium">Client Type <span class="text-danger">*</span></label>
                        <select name="client_type" class="form-select" required>
                            <option value="company" {{ old('client_type', $client->client_type) === 'company' ? 'selected' : '' }}>Company</option>
                            <option value="individual" {{ old('client_type', $client->client_type) === 'individual' ? 'selected' : '' }}>Individual</option>
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fs-14 fw-medium">Status <span class="text-danger">*</span></label>
                        <select name="status" class="form-select" required>
                            <option value="active" {{ old('status', $client->status) === 'active' ? 'selected' : '' }}>Active</option>
                            <option value="inactive" {{ old('status', $client->status) === 'inactive' ? 'selected' : '' }}>Inactive</option>
                        </select>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label fs-14 fw-medium">Company Name <span class="text-danger">*</span></label>
                        <input type="text" name="company_name" class="form-control" value="{{ old('company_name', $client->company_name) }}" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fs-14 fw-medium">Contact Person</label>
                        <input type="text" name="contact_person" class="form-control" value="{{ old('contact_person', $client->contact_person) }}">
                    </div>

                    <div class="col-md-6">
                        <label class="form-label fs-14 fw-medium">Primary Email <span class="text-danger">*</span></label>
                        <input type="email" name="email" class="form-control" value="{{ old('email', $client->email) }}" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fs-14 fw-medium">Secondary Email</label>
                        <input type="email" name="secondary_email" class="form-control" value="{{ old('secondary_email', $client->secondary_email) }}">
                    </div>

                    <div class="col-md-4">
                        <label class="form-label fs-14 fw-medium">Phone</label>
                        <input type="text" name="phone" class="form-control" value="{{ old('phone', $client->phone) }}">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fs-14 fw-medium">WhatsApp</label>
                        <input type="text" name="whatsapp" class="form-control" value="{{ old('whatsapp', $client->whatsapp) }}">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fs-14 fw-medium">Website</label>
                        <input type="url" name="website" class="form-control" value="{{ old('website', $client->website) }}">
                    </div>
                </div>
            </div>

            <div class="card-custom p-4">
                <h6 class="fw-bold border-bottom pb-2 mb-3"><i class="fa-solid fa-location-dot me-2 text-primary"></i> Billing Address</h6>
                <div class="row g-3">
                    <div class="col-12">
                        <label class="form-label fs-14 fw-medium">Street Address</label>
                        <input type="text" name="address" class="form-control" value="{{ old('address', $client->address) }}">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label fs-14 fw-medium">City</label>
                        <input type="text" name="city" class="form-control" value="{{ old('city', $client->city) }}">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label fs-14 fw-medium">State/Province</label>
                        <input type="text" name="state" class="form-control" value="{{ old('state', $client->state) }}">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label fs-14 fw-medium">Zip/Postal Code</label>
                        <input type="text" name="zip_code" class="form-control" value="{{ old('zip_code', $client->zip_code) }}">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label fs-14 fw-medium">Country</label>
                        <input type="text" name="country" class="form-control" value="{{ old('country', $client->country) }}">
                    </div>
                </div>
            </div>
        </div>

        <div class="col-12 col-lg-4">
            <div class="card-custom p-4 mb-4">
                <h6 class="fw-bold border-bottom pb-2 mb-3"><i class="fa-solid fa-sliders me-2 text-primary"></i> Financial Preferences</h6>
                <div class="mb-3">
                    <label class="form-label fs-14 fw-medium">Preferred Currency <span class="text-danger">*</span></label>
                    <select name="currency" class="form-select" required>
                        <option value="USD" {{ old('currency', $client->currency) === 'USD' ? 'selected' : '' }}>USD ($)</option>
                        <option value="EUR" {{ old('currency', $client->currency) === 'EUR' ? 'selected' : '' }}>EUR (€)</option>
                        <option value="GBP" {{ old('currency', $client->currency) === 'GBP' ? 'selected' : '' }}>GBP (£)</option>
                        <option value="INR" {{ old('currency', $client->currency) === 'INR' ? 'selected' : '' }}>INR (₹)</option>
                        <option value="CAD" {{ old('currency', $client->currency) === 'CAD' ? 'selected' : '' }}>CAD (CA$)</option>
                        <option value="AUD" {{ old('currency', $client->currency) === 'AUD' ? 'selected' : '' }}>AUD (A$)</option>
                        <option value="AED" {{ old('currency', $client->currency) === 'AED' ? 'selected' : '' }}>AED</option>
                    </select>
                </div>

                <div class="mb-3">
                    <label class="form-label fs-14 fw-medium">Payment Terms <span class="text-danger">*</span></label>
                    <select name="payment_terms" class="form-select" required>
                        <option value="Due on Receipt" {{ old('payment_terms', $client->payment_terms) === 'Due on Receipt' ? 'selected' : '' }}>Due on Receipt</option>
                        <option value="Net 7" {{ old('payment_terms', $client->payment_terms) === 'Net 7' ? 'selected' : '' }}>Net 7</option>
                        <option value="Net 15" {{ old('payment_terms', $client->payment_terms) === 'Net 15' ? 'selected' : '' }}>Net 15</option>
                        <option value="Net 30" {{ old('payment_terms', $client->payment_terms) === 'Net 30' ? 'selected' : '' }}>Net 30</option>
                        <option value="Net 45" {{ old('payment_terms', $client->payment_terms) === 'Net 45' ? 'selected' : '' }}>Net 45</option>
                        <option value="Net 60" {{ old('payment_terms', $client->payment_terms) === 'Net 60' ? 'selected' : '' }}>Net 60</option>
                    </select>
                </div>

                <div class="mb-3">
                    <label class="form-label fs-14 fw-medium">Tax/VAT/GST Number</label>
                    <input type="text" name="tax_number" class="form-control" value="{{ old('tax_number', $client->tax_number) }}">
                </div>

                <div class="mb-3">
                    <label class="form-label fs-14 fw-medium">Registration Number</label>
                    <input type="text" name="registration_number" class="form-control" value="{{ old('registration_number', $client->registration_number) }}">
                </div>
            </div>

            <div class="card-custom p-4 mb-4">
                <h6 class="fw-bold border-bottom pb-2 mb-3"><i class="fa-regular fa-note-sticky me-2 text-primary"></i> Internal Notes</h6>
                <textarea name="notes" class="form-control" rows="4">{{ old('notes', $client->notes) }}</textarea>
            </div>

            <button type="submit" class="btn btn-primary w-100 py-2 fw-semibold">
                <i class="fa-solid fa-floppy-disk me-1"></i> Update Client Profile
            </button>
        </div>
    </div>
</form>
@endsection
