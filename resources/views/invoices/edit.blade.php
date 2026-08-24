@extends('layouts.app')

@section('title', 'Edit Invoice — ' . $invoice->invoice_number)
@section('page-title', 'Edit Invoice')

@section('content')
<div class="mb-4">
    <a href="{{ route('invoices.show', $invoice) }}" class="text-decoration-none text-muted"><i class="fa-solid fa-arrow-left me-1"></i> Back to Invoice Preview</a>
    <h4 class="fw-bold mt-2">Edit Invoice {{ $invoice->invoice_number }}</h4>
</div>

<form action="{{ route('invoices.update', $invoice) }}" method="POST" id="invoiceForm">
    @csrf
    @method('PUT')
    <div class="row g-4">
        <!-- Main Form Column -->
        <div class="col-12 col-lg-8">
            <div class="card-custom p-4 mb-4">
                <h6 class="fw-bold border-bottom pb-2 mb-3"><i class="fa-solid fa-user-check me-2 text-primary"></i> Client & Invoice Details</h6>
                
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label fs-14 fw-medium">Select Client <span class="text-danger">*</span></label>
                        <select name="client_id" id="client_id" class="form-select" required>
                            @foreach($clients as $c)
                                <option value="{{ $c->id }}" data-currency="{{ $c->currency }}" data-terms="{{ $c->payment_terms }}" {{ old('client_id', $invoice->client_id) == $c->id ? 'selected' : '' }}>{{ $c->company_name }} ({{ $c->client_id }})</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label fs-14 fw-medium">Linked Project</label>
                        <select name="project_id" id="project_id" class="form-select">
                            <option value="">-- Optional Project --</option>
                            @foreach($projects as $p)
                                <option value="{{ $p->id }}" data-client-id="{{ $p->client_id }}" {{ old('project_id', $invoice->project_id) == $p->id ? 'selected' : '' }}>{{ $p->project_name }} ({{ $p->project_id }})</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-4">
                        <label class="form-label fs-14 fw-medium">Invoice Number</label>
                        <input type="text" class="form-control bg-light" value="{{ $invoice->invoice_number }}" disabled>
                    </div>

                    <div class="col-md-4">
                        <label class="form-label fs-14 fw-medium">Invoice Date <span class="text-danger">*</span></label>
                        <input type="date" name="invoice_date" class="form-control" value="{{ old('invoice_date', $invoice->invoice_date->format('Y-m-d')) }}" required>
                    </div>

                    <div class="col-md-4">
                        <label class="form-label fs-14 fw-medium">Due Date <span class="text-danger">*</span></label>
                        <input type="date" name="due_date" class="form-control" value="{{ old('due_date', $invoice->due_date->format('Y-m-d')) }}" required>
                    </div>

                    <div class="col-md-3">
                        <label class="form-label fs-14 fw-medium">Status <span class="text-danger">*</span></label>
                        <select name="status" class="form-select" required>
                            <option value="Draft" {{ old('status', $invoice->status) === 'Draft' ? 'selected' : '' }}>Draft</option>
                            <option value="Sent" {{ old('status', $invoice->status) === 'Sent' ? 'selected' : '' }}>Sent</option>
                            <option value="Partially Paid" {{ old('status', $invoice->status) === 'Partially Paid' ? 'selected' : '' }}>Partially Paid</option>
                            <option value="Paid" {{ old('status', $invoice->status) === 'Paid' ? 'selected' : '' }}>Paid</option>
                            <option value="Overdue" {{ old('status', $invoice->status) === 'Overdue' ? 'selected' : '' }}>Overdue</option>
                            <option value="Cancelled" {{ old('status', $invoice->status) === 'Cancelled' ? 'selected' : '' }}>Cancelled</option>
                        </select>
                    </div>

                    <div class="col-md-3">
                        <label class="form-label fs-14 fw-medium">Currency <span class="text-danger">*</span></label>
                        <select name="currency" class="form-select" required>
                            @foreach($currencies as $curr)
                                <option value="{{ $curr->code }}" {{ old('currency', $invoice->currency) === $curr->code ? 'selected' : '' }}>{{ $curr->code }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-3">
                        <label class="form-label fs-14 fw-medium">Payment Terms</label>
                        <input type="text" name="payment_terms" class="form-control" value="{{ old('payment_terms', $invoice->payment_terms) }}">
                    </div>

                    <div class="col-md-3">
                        <label class="form-label fs-14 fw-medium">Reference #</label>
                        <input type="text" name="reference_number" class="form-control" value="{{ old('reference_number', $invoice->reference_number) }}">
                    </div>
                </div>
            </div>

            <!-- Dynamic Invoice Items Card -->
            <div class="card-custom p-4 mb-4">
                <div class="d-flex align-items-center justify-content-between border-bottom pb-2 mb-3">
                    <h6 class="fw-bold mb-0"><i class="fa-solid fa-list-check me-2 text-primary"></i> Invoice Line Items</h6>
                    <button type="button" class="btn btn-sm btn-outline-primary" id="addItemBtn">
                        <i class="fa-solid fa-plus me-1"></i> Add Line Item
                    </button>
                </div>

                <div class="table-responsive">
                    <table class="table align-middle" id="itemsTable">
                        <thead class="table-light fs-13 text-muted">
                            <tr>
                                <th style="width: 30%;">Item / Service</th>
                                <th style="width: 15%;">Qty</th>
                                <th style="width: 15%;">Unit</th>
                                <th style="width: 18%;">Rate ($)</th>
                                <th style="width: 17%;">Amount ($)</th>
                                <th style="width: 5%;"></th>
                            </tr>
                        </thead>
                        <tbody id="itemsContainer">
                            @foreach($invoice->items as $idx => $item)
                            <tr class="item-row">
                                <td>
                                    <input type="text" name="items[{{ $idx }}][item_name]" class="form-control item-name" value="{{ $item->item_name }}" required>
                                </td>
                                <td>
                                    <input type="number" step="0.01" name="items[{{ $idx }}][quantity]" class="form-control item-qty" value="{{ $item->quantity }}" required>
                                </td>
                                <td>
                                    <input type="text" name="items[{{ $idx }}][unit]" class="form-control item-unit" value="{{ $item->unit }}">
                                </td>
                                <td>
                                    <input type="number" step="0.01" name="items[{{ $idx }}][unit_price]" class="form-control item-rate" value="{{ $item->unit_price }}" required>
                                </td>
                                <td>
                                    <input type="text" class="form-control bg-light item-amount fw-bold" value="{{ number_format($item->total_amount, 2) }}" readonly>
                                </td>
                                <td>
                                    <button type="button" class="btn btn-sm btn-light text-danger remove-item-btn"><i class="fa-solid fa-times"></i></button>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Notes & Terms -->
            <div class="card-custom p-4">
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label fs-14 fw-medium">Invoice Notes</label>
                        <textarea name="notes" class="form-control" rows="3">{{ old('notes', $invoice->notes) }}</textarea>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fs-14 fw-medium">Terms & Conditions</label>
                        <textarea name="terms_conditions" class="form-control" rows="3">{{ old('terms_conditions', $invoice->terms_conditions) }}</textarea>
                    </div>
                </div>
            </div>
        </div>

        <!-- Calculations Column -->
        <div class="col-12 col-lg-4">
            <div class="card-custom p-4 mb-4">
                <h6 class="fw-bold border-bottom pb-2 mb-3"><i class="fa-solid fa-calculator me-2 text-primary"></i> Invoice Calculations</h6>
                
                <div class="mb-3 d-flex justify-content-between align-items-center">
                    <span class="text-muted fs-14">Subtotal:</span>
                    <span class="fw-bold fs-15" id="calcSubtotal">${{ number_format($invoice->subtotal, 2) }}</span>
                </div>

                <div class="mb-3">
                    <label class="form-label fs-14 fw-medium">Discount</label>
                    <div class="input-group">
                        <select name="discount_type" id="discount_type" class="form-select" style="max-width: 90px;">
                            <option value="fixed" {{ $invoice->discount_type === 'fixed' ? 'selected' : '' }}>Fixed</option>
                            <option value="percentage" {{ $invoice->discount_type === 'percentage' ? 'selected' : '' }}>%</option>
                        </select>
                        <input type="number" step="0.01" name="discount_value" id="discount_value" class="form-control" value="{{ $invoice->discount_value }}">
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label fs-14 fw-medium">Apply Taxes</label>
                    @php $selectedTaxIds = $invoice->taxes->pluck('tax_id')->toArray(); @endphp
                    @foreach($taxes as $tax)
                        <div class="form-check">
                            <input class="form-check-input tax-checkbox" type="checkbox" name="selected_taxes[]" value="{{ $tax->id }}" id="tax_{{ $tax->id }}" {{ in_array($tax->id, $selectedTaxIds) ? 'checked' : '' }}>
                            <label class="form-check-label fs-14" for="tax_{{ $tax->id }}">
                                {{ $tax->name }} ({{ $tax->rate }}%)
                            </label>
                        </div>
                    @endforeach
                </div>

                <div class="mb-3">
                    <label class="form-label fs-14 fw-medium">Additional Charges</label>
                    <input type="number" step="0.01" name="additional_charges" id="additional_charges" class="form-control" value="{{ $invoice->additional_charges }}">
                </div>

                <div class="mb-3">
                    <label class="form-label fs-14 fw-medium">Round Off</label>
                    <input type="number" step="0.01" name="round_off" id="round_off" class="form-control" value="{{ $invoice->round_off }}">
                </div>

                <hr>

                <div class="mb-3 d-flex justify-content-between align-items-center">
                    <span class="fw-bold fs-16 text-dark">Grand Total:</span>
                    <span class="fw-bold fs-18 text-primary" id="calcGrandTotal">${{ number_format($invoice->grand_total, 2) }}</span>
                </div>

                <div class="mb-3 d-flex justify-content-between align-items-center">
                    <span class="text-muted fs-14">Already Paid:</span>
                    <span class="fw-bold fs-15 text-success">${{ number_format($invoice->paid_amount, 2) }}</span>
                </div>

                <div class="mb-3 d-flex justify-content-between align-items-center">
                    <span class="text-muted fs-14">Balance Due:</span>
                    <span class="fw-bold fs-16 text-danger" id="calcBalanceDue">${{ number_format($invoice->balance_due, 2) }}</span>
                </div>
            </div>

            <div class="card-custom p-4">
                <div class="mb-4">
                    <label class="form-label fs-14 fw-medium">PDF Template Style</label>
                    <select name="template" class="form-select">
                        <option value="modern" {{ $invoice->template === 'modern' ? 'selected' : '' }}>Modern Minimal</option>
                        <option value="corporate" {{ $invoice->template === 'corporate' ? 'selected' : '' }}>Corporate Business</option>
                    </select>
                </div>

                <button type="submit" class="btn btn-primary w-100 py-2 fw-semibold">
                    <i class="fa-solid fa-floppy-disk me-1"></i> Update Invoice
                </button>
            </div>
        </div>
    </div>
</form>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const clientSelect = document.getElementById('client_id');
        const projectSelect = document.getElementById('project_id');

        // Store initial project list from server
        const allProjects = Array.from(projectSelect.querySelectorAll('option')).map(opt => ({
            value: opt.value,
            text: opt.textContent.trim(),
            clientId: opt.getAttribute('data-client-id')
        }));

        function updateProjectOptions(preserveSelection = true) {
            const selectedClientId = clientSelect.value;
            const currentSelectedValue = preserveSelection ? projectSelect.value : '';

            projectSelect.innerHTML = '';

            const defaultOpt = document.createElement('option');
            defaultOpt.value = '';
            defaultOpt.textContent = '-- Optional Project --';
            projectSelect.appendChild(defaultOpt);

            let matchFound = false;

            allProjects.forEach(prj => {
                if (!prj.value) return;

                if (selectedClientId && prj.clientId == selectedClientId) {
                    const opt = document.createElement('option');
                    opt.value = prj.value;
                    opt.textContent = prj.text;
                    opt.setAttribute('data-client-id', prj.clientId);
                    if (prj.value == currentSelectedValue) {
                        opt.selected = true;
                        matchFound = true;
                    }
                    projectSelect.appendChild(opt);
                }
            });

            if (!matchFound) {
                projectSelect.value = '';
            }
        }

        // Auto client currency & payment terms fill + project filter
        clientSelect.addEventListener('change', function () {
            const selected = this.options[this.selectedIndex];
            if (selected) {
                const curr = selected.getAttribute('data-currency');
                const terms = selected.getAttribute('data-terms');
                if (curr && document.getElementById('currency')) {
                    document.getElementById('currency').value = curr;
                }
                if (terms && document.getElementById('payment_terms')) {
                    document.getElementById('payment_terms').value = terms;
                }
            }
            updateProjectOptions(false);
        });

        // Initialize project list for selected client
        updateProjectOptions(true);

        const itemsContainer = document.getElementById('itemsContainer');
        const addItemBtn = document.getElementById('addItemBtn');

        let itemIndex = {{ $invoice->items->count() }};

        addItemBtn.addEventListener('click', function () {
            const tr = document.createElement('tr');
            tr.className = 'item-row';
            tr.innerHTML = `
                <td>
                    <input type="text" name="items[${itemIndex}][item_name]" class="form-control item-name" placeholder="Item description..." required>
                </td>
                <td>
                    <input type="number" step="0.01" name="items[${itemIndex}][quantity]" class="form-control item-qty" value="1.00" min="0.01" required>
                </td>
                <td>
                    <input type="text" name="items[${itemIndex}][unit]" class="form-control item-unit" value="Item">
                </td>
                <td>
                    <input type="number" step="0.01" name="items[${itemIndex}][unit_price]" class="form-control item-rate" value="0.00" required>
                </td>
                <td>
                    <input type="text" class="form-control bg-light item-amount fw-bold" value="0.00" readonly>
                </td>
                <td>
                    <button type="button" class="btn btn-sm btn-light text-danger remove-item-btn"><i class="fa-solid fa-times"></i></button>
                </td>
            `;
            itemsContainer.appendChild(tr);
            itemIndex++;
        });

        itemsContainer.addEventListener('click', function (e) {
            if (e.target.closest('.remove-item-btn')) {
                if (document.querySelectorAll('.item-row').length > 1) {
                    e.target.closest('tr').remove();
                }
            }
        });
    });
</script>
@endpush
