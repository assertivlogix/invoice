@extends('layouts.app')

@section('title', 'System Settings')
@section('page-title', 'Company & System Settings')

@section('content')
<div class="mb-4">
    <h5 class="fw-bold mb-1">System Configuration</h5>
    <small class="text-muted">Manage company profile, invoice defaults, tax rates, payment gateways, and staff users</small>
</div>

<div class="row g-4">
    <div class="col-12 col-lg-3">
        <div class="card-custom p-3">
            <div class="nav flex-column nav-pills" id="settingsTabs" role="tablist">
                <button class="nav-link active text-start fw-semibold py-2 px-3 mb-1" id="company-tab" data-bs-toggle="pill" data-bs-target="#company" type="button">
                    <i class="fa-solid fa-building me-2"></i> Company Profile
                </button>
                <button class="nav-link text-start fw-semibold py-2 px-3 mb-1" id="invoice-tab" data-bs-toggle="pill" data-bs-target="#invoice" type="button">
                    <i class="fa-solid fa-file-invoice me-2"></i> Invoice Defaults
                </button>
                <button class="nav-link text-start fw-semibold py-2 px-3 mb-1" id="tax-tab" data-bs-toggle="pill" data-bs-target="#tax" type="button">
                    <i class="fa-solid fa-percent me-2"></i> Tax Rates
                </button>
                <button class="nav-link text-start fw-semibold py-2 px-3 mb-1" id="currency-tab" data-bs-toggle="pill" data-bs-target="#currency" type="button">
                    <i class="fa-solid fa-coins me-2"></i> Currencies
                </button>
                <button class="nav-link text-start fw-semibold py-2 px-3 mb-1" id="gateways-tab" data-bs-toggle="pill" data-bs-target="#gateways" type="button">
                    <i class="fa-solid fa-credit-card me-2"></i> Payment Gateways
                </button>
                <button class="nav-link text-start fw-semibold py-2 px-3 mb-1" id="users-tab" data-bs-toggle="pill" data-bs-target="#users" type="button">
                    <i class="fa-solid fa-user-gear me-2"></i> Staff & Users
                </button>
            </div>
        </div>
    </div>

    <div class="col-12 col-lg-9">
        <div class="tab-content" id="settingsTabsContent">
            <!-- Company Profile Tab -->
            <div class="tab-pane fade show active" id="company">
                <div class="card-custom p-4">
                    <h6 class="fw-bold border-bottom pb-2 mb-3">Company Profile & Branding</h6>
                    <form action="{{ route('settings.update_company') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label fs-14 fw-medium">Company Name <span class="text-danger">*</span></label>
                                <input type="text" name="company_name" class="form-control" value="{{ old('company_name', $company->company_name) }}" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fs-14 fw-medium">Email Address</label>
                                <input type="email" name="email" class="form-control" value="{{ old('email', $company->email) }}">
                            </div>

                            <div class="col-md-4">
                                <label class="form-label fs-14 fw-medium">Phone</label>
                                <input type="text" name="phone" class="form-control" value="{{ old('phone', $company->phone) }}">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fs-14 fw-medium">Website</label>
                                <input type="url" name="website" class="form-control" value="{{ old('website', $company->website) }}">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fs-14 fw-medium">Tax / VAT ID</label>
                                <input type="text" name="tax_number" class="form-control" value="{{ old('tax_number', $company->tax_number) }}">
                            </div>

                            <div class="col-12">
                                <label class="form-label fs-14 fw-medium">Street Address</label>
                                <input type="text" name="address" class="form-control" value="{{ old('address', $company->address) }}">
                            </div>

                            <div class="col-md-3">
                                <label class="form-label fs-14 fw-medium">City</label>
                                <input type="text" name="city" class="form-control" value="{{ old('city', $company->city) }}">
                            </div>
                            <div class="col-md-3">
                                <label class="form-label fs-14 fw-medium">State</label>
                                <input type="text" name="state" class="form-control" value="{{ old('state', $company->state) }}">
                            </div>
                            <div class="col-md-3">
                                <label class="form-label fs-14 fw-medium">Zip Code</label>
                                <input type="text" name="zip_code" class="form-control" value="{{ old('zip_code', $company->zip_code) }}">
                            </div>
                            <div class="col-md-3">
                                <label class="form-label fs-14 fw-medium">Country</label>
                                <input type="text" name="country" class="form-control" value="{{ old('country', $company->country) }}">
                            </div>

                            <h6 class="fw-bold border-bottom pb-2 mt-4 mb-2">Bank & Payment Details</h6>
                            <div class="col-md-6">
                                <label class="form-label fs-14 fw-medium">Bank Name</label>
                                <input type="text" name="bank_name" class="form-control" value="{{ old('bank_name', $company->bank_name) }}">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fs-14 fw-medium">Account Name</label>
                                <input type="text" name="account_name" class="form-control" value="{{ old('account_name', $company->account_name) }}">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fs-14 fw-medium">Account Number</label>
                                <input type="text" name="account_number" class="form-control" value="{{ old('account_number', $company->account_number) }}">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fs-14 fw-medium">SWIFT / IFSC Code</label>
                                <input type="text" name="ifsc_swift" class="form-control" value="{{ old('ifsc_swift', $company->ifsc_swift) }}">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fs-14 fw-medium">UPI ID</label>
                                <input type="text" name="upi_id" class="form-control" value="{{ old('upi_id', $company->upi_id) }}">
                            </div>

                            <div class="col-12 mt-4">
                                <button type="submit" class="btn btn-primary px-4 fw-semibold"><i class="fa-solid fa-floppy-disk me-1"></i> Save Company Settings</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Invoice Defaults Tab -->
            <div class="tab-pane fade" id="invoice">
                <div class="card-custom p-4">
                    <h6 class="fw-bold border-bottom pb-2 mb-3">Invoice Numbering & Defaults</h6>
                    <form action="{{ route('settings.update_invoice') }}" method="POST">
                        @csrf
                        <div class="row g-3">
                            <div class="col-md-4">
                                <label class="form-label fs-14 fw-medium">Invoice Number Prefix <span class="text-danger">*</span></label>
                                <input type="text" name="invoice_prefix" class="form-control" value="{{ old('invoice_prefix', $company->invoice_prefix) }}" required>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fs-14 fw-medium">Next Starting Number <span class="text-danger">*</span></label>
                                <input type="number" name="invoice_starting_number" class="form-control" value="{{ old('invoice_starting_number', $company->invoice_starting_number) }}" required>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fs-14 fw-medium">Default Payment Terms <span class="text-danger">*</span></label>
                                <input type="text" name="payment_terms" class="form-control" value="{{ old('payment_terms', $company->payment_terms) }}" required>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fs-14 fw-medium">Default Template Style</label>
                                <select name="default_template" class="form-select">
                                    <option value="modern" {{ $company->default_template === 'modern' ? 'selected' : '' }}>Modern Minimal</option>
                                    <option value="corporate" {{ $company->default_template === 'corporate' ? 'selected' : '' }}>Corporate Business</option>
                                </select>
                            </div>

                            <div class="col-12">
                                <label class="form-label fs-14 fw-medium">Default Invoice Notes</label>
                                <textarea name="default_invoice_notes" class="form-control" rows="3">{{ old('default_invoice_notes', $company->default_invoice_notes) }}</textarea>
                            </div>
                            <div class="col-12">
                                <label class="form-label fs-14 fw-medium">Default Invoice Footer & Terms</label>
                                <textarea name="default_invoice_footer" class="form-control" rows="3">{{ old('default_invoice_footer', $company->default_invoice_footer) }}</textarea>
                            </div>

                            <div class="col-12 mt-4">
                                <button type="submit" class="btn btn-primary px-4 fw-semibold"><i class="fa-solid fa-floppy-disk me-1"></i> Save Invoice Defaults</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Tax Rates Tab -->
            <div class="tab-pane fade" id="tax">
                <div class="card-custom p-4 mb-4">
                    <h6 class="fw-bold border-bottom pb-2 mb-3">Add Tax Rate</h6>
                    <form action="{{ route('settings.store_tax') }}" method="POST">
                        @csrf
                        <div class="row g-3">
                            <div class="col-md-4">
                                <label class="form-label fs-14 fw-medium">Tax Name <span class="text-danger">*</span></label>
                                <input type="text" name="name" class="form-control" placeholder="e.g. GST 18% or VAT 20%" required>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fs-14 fw-medium">Tax Rate (%) <span class="text-danger">*</span></label>
                                <input type="number" step="0.01" name="rate" class="form-control" placeholder="18.00" required>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fs-14 fw-medium">Tax Type <span class="text-danger">*</span></label>
                                <select name="type" class="form-select" required>
                                    <option value="GST">GST</option>
                                    <option value="CGST">CGST</option>
                                    <option value="SGST">SGST</option>
                                    <option value="IGST">IGST</option>
                                    <option value="VAT">VAT</option>
                                    <option value="Sales Tax">Sales Tax</option>
                                    <option value="Custom">Custom</option>
                                </select>
                            </div>
                            <div class="col-12 text-end">
                                <button type="submit" class="btn btn-primary px-4 fw-semibold"><i class="fa-solid fa-plus me-1"></i> Add Tax Rate</button>
                            </div>
                        </div>
                    </form>
                </div>

                <div class="card-custom p-4">
                    <h6 class="fw-bold border-bottom pb-2 mb-3">Configured Tax Rates</h6>
                    <div class="table-responsive">
                        <table class="table align-middle">
                            <thead>
                                <tr>
                                    <th>Tax Name</th>
                                    <th>Type</th>
                                    <th>Rate (%)</th>
                                    <th class="text-end">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($taxes as $t)
                                <tr>
                                    <td class="fw-bold">{{ $t->name }}</td>
                                    <td><span class="badge bg-light text-dark border">{{ $t->type }}</span></td>
                                    <td class="fw-bold text-primary">{{ $t->rate }}%</td>
                                    <td class="text-end">
                                        <form action="{{ route('settings.delete_tax', $t) }}" method="POST" class="d-inline" onsubmit="return confirm('Delete tax rate?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-light border text-danger"><i class="fa-regular fa-trash-can"></i></button>
                                        </form>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Currencies Tab -->
            <div class="tab-pane fade" id="currency">
                <div class="card-custom p-4 mb-4">
                    <h6 class="fw-bold border-bottom pb-2 mb-3">Add Currency</h6>
                    <form action="{{ route('settings.store_currency') }}" method="POST">
                        @csrf
                        <div class="row g-3">
                            <div class="col-md-3">
                                <label class="form-label fs-14 fw-medium">Code <span class="text-danger">*</span></label>
                                <input type="text" name="code" class="form-control" placeholder="USD" required>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fs-14 fw-medium">Currency Name <span class="text-danger">*</span></label>
                                <input type="text" name="name" class="form-control" placeholder="US Dollar" required>
                            </div>
                            <div class="col-md-2">
                                <label class="form-label fs-14 fw-medium">Symbol <span class="text-danger">*</span></label>
                                <input type="text" name="symbol" class="form-control" placeholder="$" required>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label fs-14 fw-medium">Exchange Rate</label>
                                <input type="number" step="0.000001" name="exchange_rate" class="form-control" value="1.000000">
                            </div>
                            <div class="col-12 text-end">
                                <button type="submit" class="btn btn-primary px-4 fw-semibold"><i class="fa-solid fa-plus me-1"></i> Add Currency</button>
                            </div>
                        </div>
                    </form>
                </div>

                <div class="card-custom p-4">
                    <h6 class="fw-bold border-bottom pb-2 mb-3">Supported Currencies</h6>
                    <div class="table-responsive">
                        <table class="table align-middle">
                            <thead>
                                <tr>
                                    <th>Code</th>
                                    <th>Currency Name</th>
                                    <th>Symbol</th>
                                    <th>Exchange Rate</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($currencies as $curr)
                                <tr>
                                    <td class="fw-bold text-primary">{{ $curr->code }}</td>
                                    <td>{{ $curr->name }}</td>
                                    <td class="fw-bold fs-16">{{ $curr->symbol }}</td>
                                    <td>{{ $curr->exchange_rate }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Gateways Tab -->
            <div class="tab-pane fade" id="gateways">
                <div class="card-custom p-4">
                    <h6 class="fw-bold border-bottom pb-2 mb-3">Payment Gateway Integration Settings</h6>
                    <form action="{{ route('settings.update_gateways') }}" method="POST">
                        @csrf
                        
                        <!-- Stripe -->
                        <div class="border rounded-3 p-3 mb-3 bg-light">
                            <div class="d-flex align-items-center justify-content-between mb-2">
                                <h6 class="fw-bold mb-0 text-primary"><i class="fa-brands fa-stripe me-2 fa-lg"></i> Stripe Gateway</h6>
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" name="gateways[stripe][is_active]" value="1" id="stripe_active" checked>
                                    <label class="form-check-label fw-semibold" for="stripe_active">Enable Stripe</label>
                                </div>
                            </div>
                            <div class="row g-2">
                                <div class="col-md-6">
                                    <label class="form-label fs-13 text-muted">Publishable / API Key</label>
                                    <input type="text" name="gateways[stripe][api_key]" class="form-control fs-13" value="pk_test_51Nx...example">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fs-13 text-muted">Secret Key</label>
                                    <input type="password" name="gateways[stripe][secret_key]" class="form-control fs-13" value="sk_test_51Nx...example">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fs-13 text-muted">Webhook Secret</label>
                                    <input type="password" name="gateways[stripe][webhook_secret]" class="form-control fs-13" value="whsec_...example">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fs-13 text-muted">Environment</label>
                                    <select name="gateways[stripe][environment]" class="form-select fs-13">
                                        <option value="test" selected>Test / Sandbox</option>
                                        <option value="live">Live Production</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <!-- Razorpay -->
                        <div class="border rounded-3 p-3 mb-3 bg-light">
                            <div class="d-flex align-items-center justify-content-between mb-2">
                                <h6 class="fw-bold mb-0 text-warning"><i class="fa-solid fa-bolt me-2"></i> Razorpay Gateway</h6>
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" name="gateways[razorpay][is_active]" value="1" id="razorpay_active" checked>
                                    <label class="form-check-label fw-semibold" for="razorpay_active">Enable Razorpay</label>
                                </div>
                            </div>
                            <div class="row g-2">
                                <div class="col-md-6">
                                    <label class="form-label fs-13 text-muted">Key ID</label>
                                    <input type="text" name="gateways[razorpay][api_key]" class="form-control fs-13" value="rzp_test_...example">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fs-13 text-muted">Key Secret</label>
                                    <input type="password" name="gateways[razorpay][secret_key]" class="form-control fs-13" value="rzp_secret_...example">
                                </div>
                            </div>
                        </div>

                        <button type="submit" class="btn btn-primary px-4 fw-semibold"><i class="fa-solid fa-floppy-disk me-1"></i> Save Gateway Credentials</button>
                    </form>
                </div>
            </div>

            <!-- Users & Roles Tab -->
            <div class="tab-pane fade" id="users">
                <div class="card-custom p-4 mb-4">
                    <h6 class="fw-bold border-bottom pb-2 mb-3">Add Staff / Admin User Account</h6>
                    <form action="{{ route('settings.store_user') }}" method="POST">
                        @csrf
                        <div class="row g-3">
                            <div class="col-md-4">
                                <label class="form-label fs-14 fw-medium">Full Name <span class="text-danger">*</span></label>
                                <input type="text" name="name" class="form-control" required>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fs-14 fw-medium">Email Address <span class="text-danger">*</span></label>
                                <input type="email" name="email" class="form-control" required>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fs-14 fw-medium">Password <span class="text-danger">*</span></label>
                                <input type="password" name="password" class="form-control" required minlength="8">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fs-14 fw-medium">Role <span class="text-danger">*</span></label>
                                <select name="role" class="form-select" required>
                                    <option value="staff">Staff (Create & View Invoices)</option>
                                    <option value="admin">Administrator (Full Access)</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fs-14 fw-medium">Phone Number</label>
                                <input type="text" name="phone" class="form-control">
                            </div>
                            <div class="col-12 text-end">
                                <button type="submit" class="btn btn-primary px-4 fw-semibold"><i class="fa-solid fa-user-plus me-1"></i> Create User</button>
                            </div>
                        </div>
                    </form>
                </div>

                <div class="card-custom p-4">
                    <h6 class="fw-bold border-bottom pb-2 mb-3">System Accounts</h6>
                    <div class="table-responsive">
                        <table class="table align-middle">
                            <thead>
                                <tr>
                                    <th>Name</th>
                                    <th>Email</th>
                                    <th>Role</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($users as $u)
                                <tr>
                                    <td class="fw-bold">{{ $u->name }}</td>
                                    <td>{{ $u->email }}</td>
                                    <td>
                                        <span class="badge {{ $u->role === 'admin' ? 'bg-primary' : 'bg-secondary' }}">
                                            {{ ucfirst($u->role) }}
                                        </span>
                                    </td>
                                    <td><span class="badge bg-success-subtle text-success">Active</span></td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
