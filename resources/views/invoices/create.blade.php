@extends('layouts.app')

@section('title', 'Create Invoice')
@section('page-title', 'Create New Invoice')

@section('content')
<div class="mb-4">
    <a href="{{ route('invoices.index') }}" class="text-decoration-none text-muted"><i class="fa-solid fa-arrow-left me-1"></i> Back to Invoices</a>
    <h4 class="fw-bold mt-2">New Invoice</h4>
</div>

<form action="{{ route('invoices.store') }}" method="POST" id="invoiceForm">
    @csrf
    <div class="row g-4">
        <!-- Main Form Column -->
        <div class="col-12 col-lg-8">
            <!-- Client & Invoice Info Card -->
            <div class="card-custom p-4 mb-4">
                <h6 class="fw-bold border-bottom pb-2 mb-3"><i class="fa-solid fa-user-check me-2 text-primary"></i> Client & Invoice Details</h6>
                
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label fs-14 fw-medium">Select Client <span class="text-danger">*</span></label>
                        <select name="client_id" id="client_id" class="form-select" required>
                            <option value="">-- Select Client --</option>
                            @foreach($clients as $c)
                                <option value="{{ $c->id }}" 
                                    data-currency="{{ $c->currency }}" 
                                    data-terms="{{ $c->payment_terms }}"
                                    {{ (old('client_id', $selectedClient ? $selectedClient->id : '') == $c->id) ? 'selected' : '' }}>
                                    {{ $c->company_name }} ({{ $c->client_id }})
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label fs-14 fw-medium">Linked Project</label>
                        <select name="project_id" id="project_id" class="form-select">
                            <option value="">-- Optional Project --</option>
                            @foreach($projects as $p)
                                <option value="{{ $p->id }}" {{ old('project_id') == $p->id ? 'selected' : '' }}>{{ $p->project_name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-4">
                        <label class="form-label fs-14 fw-medium">Invoice Number <span class="text-danger">*</span></label>
                        <input type="text" name="invoice_number" class="form-control" value="{{ old('invoice_number', $suggestedNumber) }}" required>
                    </div>

                    <div class="col-md-4">
                        <label class="form-label fs-14 fw-medium">Invoice Date <span class="text-danger">*</span></label>
                        <input type="date" name="invoice_date" id="invoice_date" class="form-control" value="{{ old('invoice_date', date('Y-m-d')) }}" required>
                    </div>

                    <div class="col-md-4">
                        <label class="form-label fs-14 fw-medium">Due Date <span class="text-danger">*</span></label>
                        <input type="date" name="due_date" id="due_date" class="form-control" value="{{ old('due_date', date('Y-m-d', strtotime('+30 days'))) }}" required>
                    </div>

                    <div class="col-md-3">
                        <label class="form-label fs-14 fw-medium">Currency <span class="text-danger">*</span></label>
                        <select name="currency" id="currency" class="form-select" required>
                            @foreach($currencies as $curr)
                                <option value="{{ $curr->code }}" {{ old('currency', 'USD') === $curr->code ? 'selected' : '' }}>{{ $curr->code }} ({{ $curr->symbol }})</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-3">
                        <label class="form-label fs-14 fw-medium">Payment Terms <span class="text-danger">*</span></label>
                        <select name="payment_terms" id="payment_terms" class="form-select" required>
                            <option value="Due on Receipt">Due on Receipt</option>
                            <option value="Net 7">Net 7</option>
                            <option value="Net 15">Net 15</option>
                            <option value="Net 30" selected>Net 30</option>
                            <option value="Net 45">Net 45</option>
                            <option value="Net 60">Net 60</option>
                        </select>
                    </div>

                    <div class="col-md-3">
                        <label class="form-label fs-14 fw-medium">Reference #</label>
                        <input type="text" name="reference_number" class="form-control" value="{{ old('reference_number') }}" placeholder="REF-001">
                    </div>

                    <div class="col-md-3">
                        <label class="form-label fs-14 fw-medium">PO Number</label>
                        <input type="text" name="po_number" class="form-control" value="{{ old('po_number') }}" placeholder="PO-991">
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
                            <!-- Line Item Row 1 -->
                            <tr class="item-row">
                                <td>
                                    <select class="form-select service-select mb-1 fs-13">
                                        <option value="">-- Catalog Item --</option>
                                        @foreach($services as $svc)
                                            <option value="{{ $svc->id }}" data-name="{{ $svc->service_name }}" data-price="{{ $svc->default_price }}" data-unit="{{ $svc->unit }}" data-tax="{{ $svc->tax_rate }}">{{ $svc->service_name }} (${{ $svc->default_price }})</option>
                                        @endforeach
                                    </select>
                                    <input type="text" name="items[0][item_name]" class="form-control item-name" placeholder="Item description..." required>
                                </td>
                                <td>
                                    <input type="number" step="0.01" name="items[0][quantity]" class="form-control item-qty" value="1.00" min="0.01" required>
                                </td>
                                <td>
                                    <select name="items[0][unit]" class="form-select item-unit">
                                        <option value="Hour">Hour</option>
                                        <option value="Day">Day</option>
                                        <option value="Project">Project</option>
                                        <option value="Month">Month</option>
                                        <option value="License">License</option>
                                        <option value="Item" selected>Item</option>
                                    </select>
                                </td>
                                <td>
                                    <input type="number" step="0.01" name="items[0][unit_price]" class="form-control item-rate" value="0.00" required>
                                </td>
                                <td>
                                    <input type="text" class="form-control bg-light item-amount fw-bold" value="0.00" readonly>
                                </td>
                                <td>
                                    <button type="button" class="btn btn-sm btn-light text-danger remove-item-btn"><i class="fa-solid fa-times"></i></button>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Notes & Terms Card -->
            <div class="card-custom p-4">
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label fs-14 fw-medium">Invoice Notes</label>
                        <textarea name="notes" class="form-control" rows="3">{{ old('notes', $company->default_invoice_notes) }}</textarea>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fs-14 fw-medium">Terms & Conditions</label>
                        <textarea name="terms_conditions" class="form-control" rows="3">{{ old('terms_conditions', $company->default_invoice_footer) }}</textarea>
                    </div>
                </div>
            </div>
        </div>

        <!-- Right Calculation Column -->
        <div class="col-12 col-lg-4">
            <div class="card-custom p-4 mb-4">
                <h6 class="fw-bold border-bottom pb-2 mb-3"><i class="fa-solid fa-calculator me-2 text-primary"></i> Invoice Calculations</h6>
                
                <div class="mb-3 d-flex justify-content-between align-items-center">
                    <span class="text-muted fs-14">Subtotal:</span>
                    <span class="fw-bold fs-15" id="calcSubtotal">$0.00</span>
                </div>

                <!-- Discount -->
                <div class="mb-3">
                    <label class="form-label fs-14 fw-medium">Invoice Discount</label>
                    <div class="input-group">
                        <select name="discount_type" id="discount_type" class="form-select" style="max-width: 90px;">
                            <option value="fixed">Fixed</option>
                            <option value="percentage">%</option>
                        </select>
                        <input type="number" step="0.01" name="discount_value" id="discount_value" class="form-control" value="0.00">
                    </div>
                    <small class="text-muted d-block text-end mt-1" id="calcDiscountAmount">Discount: -$0.00</small>
                </div>

                <!-- Taxes -->
                <div class="mb-3">
                    <label class="form-label fs-14 fw-medium">Apply Taxes</label>
                    @foreach($taxes as $tax)
                        <div class="form-check">
                            <input class="form-check-input tax-checkbox" type="checkbox" name="selected_taxes[]" value="{{ $tax->id }}" id="tax_{{ $tax->id }}">
                            <label class="form-check-label fs-14" for="tax_{{ $tax->id }}">
                                {{ $tax->name }} ({{ $tax->rate }}%)
                            </label>
                        </div>
                    @endforeach
                    <small class="text-muted d-block text-end mt-1" id="calcTaxAmount">Tax Amount: +$0.00</small>
                </div>

                <!-- Additional Charges -->
                <div class="mb-3">
                    <label class="form-label fs-14 fw-medium">Additional Charges ($)</label>
                    <input type="number" step="0.01" name="additional_charges" id="additional_charges" class="form-control" value="0.00">
                </div>

                <!-- Round Off -->
                <div class="mb-3">
                    <label class="form-label fs-14 fw-medium">Round Off ($)</label>
                    <input type="number" step="0.01" name="round_off" id="round_off" class="form-control" value="0.00">
                </div>

                <hr>

                <!-- Grand Total -->
                <div class="mb-3 d-flex justify-content-between align-items-center">
                    <span class="fw-bold fs-16 text-dark">Grand Total:</span>
                    <span class="fw-bold fs-18 text-primary" id="calcGrandTotal">$0.00</span>
                </div>

                <div class="mb-3 d-flex justify-content-between align-items-center">
                    <span class="text-muted fs-14">Balance Due:</span>
                    <span class="fw-bold fs-16 text-danger" id="calcBalanceDue">$0.00</span>
                </div>
            </div>

            <!-- Configuration & Actions -->
            <div class="card-custom p-4">
                <div class="mb-3">
                    <label class="form-label fs-14 fw-medium">PDF Template Style</label>
                    <select name="template" class="form-select">
                        <option value="modern" selected>Modern Minimal</option>
                        <option value="corporate">Corporate Business</option>
                    </select>
                </div>

                <div class="form-check mb-4">
                    <input class="form-check-input" type="checkbox" name="send_email" value="1" id="send_email">
                    <label class="form-check-label fs-14" for="send_email">
                        Email invoice PDF to client immediately
                    </label>
                </div>

                <button type="submit" class="btn btn-primary w-100 py-2 fw-semibold">
                    <i class="fa-solid fa-paper-plane me-1"></i> Finalize & Save Invoice
                </button>
            </div>
        </div>
    </div>
</form>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const itemsContainer = document.getElementById('itemsContainer');
        const addItemBtn = document.getElementById('addItemBtn');

        let itemIndex = 1;

        // Auto client currency fill
        document.getElementById('client_id').addEventListener('change', function () {
            const selected = this.options[this.selectedIndex];
            const curr = selected.getAttribute('data-currency');
            if (curr) {
                document.getElementById('currency').value = curr;
            }
        });

        // Add line item row
        addItemBtn.addEventListener('click', function () {
            const tr = document.createElement('tr');
            tr.className = 'item-row';
            tr.innerHTML = `
                <td>
                    <select class="form-select service-select mb-1 fs-13">
                        <option value="">-- Catalog Item --</option>
                        @foreach($services as $svc)
                            <option value="{{ $svc->id }}" data-name="{{ $svc->service_name }}" data-price="{{ $svc->default_price }}" data-unit="{{ $svc->unit }}" data-tax="{{ $svc->tax_rate }}">{{ $svc->service_name }} (${{ $svc->default_price }})</option>
                        @endforeach
                    </select>
                    <input type="text" name="items[${itemIndex}][item_name]" class="form-control item-name" placeholder="Item description..." required>
                </td>
                <td>
                    <input type="number" step="0.01" name="items[${itemIndex}][quantity]" class="form-control item-qty" value="1.00" min="0.01" required>
                </td>
                <td>
                    <select name="items[${itemIndex}][unit]" class="form-select item-unit">
                        <option value="Hour">Hour</option>
                        <option value="Day">Day</option>
                        <option value="Project">Project</option>
                        <option value="Month">Month</option>
                        <option value="License">License</option>
                        <option value="Item" selected>Item</option>
                    </select>
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
            recalculate();
        });

        // Delegate row events
        itemsContainer.addEventListener('change', function (e) {
            if (e.target.classList.contains('service-select')) {
                const opt = e.target.options[e.target.selectedIndex];
                const row = e.target.closest('tr');
                if (opt.value) {
                    row.querySelector('.item-name').value = opt.getAttribute('data-name');
                    row.querySelector('.item-rate').value = opt.getAttribute('data-price');
                    row.querySelector('.item-unit').value = opt.getAttribute('data-unit');
                }
            }
            recalculate();
        });

        itemsContainer.addEventListener('input', recalculate);
        itemsContainer.addEventListener('click', function (e) {
            if (e.target.closest('.remove-item-btn')) {
                if (document.querySelectorAll('.item-row').length > 1) {
                    e.target.closest('tr').remove();
                    recalculate();
                }
            }
        });

        document.querySelectorAll('#discount_type, #discount_value, #additional_charges, #round_off, .tax-checkbox')
            .forEach(el => el.addEventListener('input', recalculate));

        function recalculate() {
            let subtotal = 0;
            document.querySelectorAll('.item-row').forEach(row => {
                const qty = parseFloat(row.querySelector('.item-qty').value) || 0;
                const rate = parseFloat(row.querySelector('.item-rate').value) || 0;
                const amount = qty * rate;
                row.querySelector('.item-amount').value = amount.toFixed(2);
                subtotal += amount;
            });

            document.getElementById('calcSubtotal').innerText = '$' + subtotal.toFixed(2);

            const discType = document.getElementById('discount_type').value;
            const discVal = parseFloat(document.getElementById('discount_value').value) || 0;
            let discAmount = 0;
            if (discType === 'percentage') {
                discAmount = (subtotal * discVal) / 100;
            } else {
                discAmount = Math.min(discVal, subtotal);
            }
            document.getElementById('calcDiscountAmount').innerText = 'Discount: -$' + discAmount.toFixed(2);

            const taxable = Math.max(0, subtotal - discAmount);

            let taxAmount = 0;
            document.querySelectorAll('.tax-checkbox:checked').forEach(cb => {
                const label = cb.nextElementSibling.innerText;
                const match = label.match(/\((\d+(\.\d+)?)%\)/);
                if (match) {
                    const rate = parseFloat(match[1]);
                    taxAmount += (taxable * rate) / 100;
                }
            });

            document.getElementById('calcTaxAmount').innerText = 'Tax Amount: +$' + taxAmount.toFixed(2);

            const addCharges = parseFloat(document.getElementById('additional_charges').value) || 0;
            const roundOff = parseFloat(document.getElementById('round_off').value) || 0;

            const grandTotal = Math.max(0, taxable + taxAmount + addCharges + roundOff);

            document.getElementById('calcGrandTotal').innerText = '$' + grandTotal.toFixed(2);
            document.getElementById('calcBalanceDue').innerText = '$' + grandTotal.toFixed(2);
        }

        recalculate();
    });
</script>
@endpush
