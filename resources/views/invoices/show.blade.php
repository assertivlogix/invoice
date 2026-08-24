@extends('layouts.app')

@section('title', 'Invoice ' . $invoice->invoice_number)
@section('page-title', 'Invoice Details: ' . $invoice->invoice_number)

@section('content')
<!-- Top Action Toolbar -->
<div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3 mb-4 d-print-none">
    <div class="d-flex align-items-center gap-2">
        <a href="{{ route('invoices.index') }}" class="btn btn-light border"><i class="fa-solid fa-arrow-left me-1"></i> Invoices</a>
        <span class="badge badge-status badge-{{ strtolower(str_replace(' ', '', $invoice->status)) }} fs-14">
            {{ $invoice->status }}
        </span>
    </div>

    <div class="d-flex flex-wrap gap-2">
        @if($invoice->status !== 'Cancelled')
            <a href="{{ route('invoices.edit', $invoice) }}" class="btn btn-outline-secondary"><i class="fa-regular fa-pen-to-square me-1"></i> Edit</a>
            <a href="{{ route('invoices.download_pdf', $invoice) }}" class="btn btn-outline-danger"><i class="fa-solid fa-file-pdf me-1"></i> Download PDF</a>
            <button onclick="window.print();" class="btn btn-outline-dark"><i class="fa-solid fa-print me-1"></i> Print</button>

            <form action="{{ route('invoices.send_email', $invoice) }}" method="POST" class="d-inline">
                @csrf
                <button type="submit" class="btn btn-outline-primary"><i class="fa-solid fa-envelope me-1"></i> Email Client</button>
            </form>

            @if($invoice->balance_due > 0)
            <a href="{{ route('payments.create', ['invoice_id' => $invoice->id]) }}" class="btn btn-success"><i class="fa-solid fa-hand-holding-dollar me-1"></i> Record Payment</a>
            @endif

            <form action="{{ route('invoices.duplicate', $invoice) }}" method="POST" class="d-inline">
                @csrf
                <button type="submit" class="btn btn-light border" title="Duplicate"><i class="fa-regular fa-copy me-1"></i> Duplicate</button>
            </form>

            @if(auth()->user()->isAdmin() && $invoice->status !== 'Paid')
            <button type="button" class="btn btn-outline-danger" data-bs-toggle="modal" data-bs-target="#cancelModal"><i class="fa-solid fa-ban me-1"></i> Cancel</button>
            @endif
        @endif
    </div>
</div>

<!-- Main Invoice Paper Preview -->
<div class="row g-4">
    <div class="col-12 col-lg-8">
        <div class="card-custom p-5 bg-white position-relative overflow-hidden" id="invoicePaper">
            @if($invoice->status === 'Paid')
                <!-- PAID Watermark -->
                <div class="position-absolute" style="top: 35%; left: 20%; transform: rotate(-25deg); opacity: 0.15; pointer-events: none;">
                    <span style="font-size: 8rem; font-weight: 900; color: #10b981; border: 12px solid #10b981; padding: 0.2rem 2rem; border-radius: 1rem;">PAID</span>
                </div>
            @endif

            <!-- Company Header -->
            <div class="row align-items-center border-bottom pb-4 mb-4">
                <div class="col-4">
                    <h5 class="fw-bold text-primary mb-1">{{ $company->company_name }}</h5>
                    <div class="text-muted fs-13">
                        {{ $company->address }}<br>
                        {{ $company->city }}, {{ $company->state }} {{ $company->zip_code }}, {{ $company->country }}<br>
                        Email: {{ $company->email }} | Phone: {{ $company->phone }}<br>
                        @if($company->tax_number) Tax ID: {{ $company->tax_number }} @endif
                    </div>
                </div>
                <div class="col-4 text-center">
                    <img src="{{ asset('images/logo.svg') }}" alt="{{ $company->company_name }}" style="max-height: 65px; max-width: 100%;">
                </div>
                <div class="col-4 text-end">
                    <h2 class="fw-bold text-dark mb-1">INVOICE</h2>
                    <div class="fs-15 fw-bold text-primary mb-2">{{ $invoice->invoice_number }}</div>
                    <div class="fs-13 text-muted">
                        <strong>Invoice Date:</strong> {{ $invoice->invoice_date->format('M d, Y') }}<br>
                        <strong>Due Date:</strong> {{ $invoice->due_date->format('M d, Y') }}<br>
                        <strong>Payment Terms:</strong> {{ $invoice->payment_terms }}<br>
                        @if($invoice->po_number) <strong>PO #:</strong> {{ $invoice->po_number }} @endif
                    </div>
                </div>
            </div>

            <!-- Client Billing Address -->
            <div class="row mb-4">
                <div class="col-6">
                    <small class="text-muted text-uppercase fw-semibold fs-12">Billed To:</small>
                    <h5 class="fw-bold mb-1 text-dark">{{ $invoice->client->company_name }}</h5>
                    @if($invoice->client->contact_person) <div class="fs-14 text-secondary">Attn: {{ $invoice->client->contact_person }}</div> @endif
                    <div class="fs-14 text-secondary">{{ $invoice->client->formatted_address }}</div>
                    <div class="fs-14 text-secondary">Email: {{ $invoice->client->email }}</div>
                    @if($invoice->client->tax_number) <div class="fs-13 text-muted">Tax ID: {{ $invoice->client->tax_number }}</div> @endif
                </div>

                @if($invoice->project)
                <div class="col-6 text-end">
                    <small class="text-muted text-uppercase fw-semibold fs-12">Project Details:</small>
                    <h6 class="fw-bold mb-1 text-dark">{{ $invoice->project->project_name }}</h6>
                    <div class="fs-13 text-muted">Project ID: {{ $invoice->project->project_id }}</div>
                </div>
                @endif
            </div>

            <!-- Items Table -->
            <div class="table-responsive mb-4">
                <table class="table table-bordered align-middle fs-14">
                    <thead class="table-light">
                        <tr>
                            <th style="width: 45%;">Description</th>
                            <th class="text-center" style="width: 12%;">Qty</th>
                            <th class="text-center" style="width: 13%;">Unit</th>
                            <th class="text-end" style="width: 15%;">Rate</th>
                            <th class="text-end" style="width: 15%;">Amount</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($invoice->items as $item)
                        <tr>
                            <td>
                                <div class="fw-bold text-dark">{{ $item->item_name }}</div>
                                @if($item->description) <small class="text-muted">{{ $item->description }}</small> @endif
                            </td>
                            <td class="text-center">{{ number_format($item->quantity, 2) }}</td>
                            <td class="text-center">{{ $item->unit }}</td>
                            <td class="text-end">{{ $invoice->currency_symbol }}{{ number_format($item->unit_price, 2) }}</td>
                            <td class="text-end fw-bold">{{ $invoice->currency_symbol }}{{ number_format($item->total_amount, 2) }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <!-- Totals & Calculations Breakdown -->
            <div class="row mb-4">
                <div class="col-6">
                    @if($company->bank_name && $invoice->status !== 'Paid')
                    <div class="p-3 bg-light rounded-3 fs-13">
                        <h6 class="fw-bold mb-2 text-dark"><i class="fa-solid fa-building-columns text-primary me-1"></i> Bank Payment Details</h6>
                        <div><strong>Bank Name:</strong> {{ $company->bank_name }}</div>
                        <div><strong>Account Name:</strong> {{ $company->account_name }}</div>
                        <div><strong>Account Number:</strong> <code>{{ $company->account_number }}</code></div>
                        <div><strong>SWIFT / IFSC:</strong> <code>{{ $company->ifsc_swift }}</code></div>
                        @if($company->upi_id) <div><strong>UPI ID:</strong> <code>{{ $company->upi_id }}</code></div> @endif
                    </div>
                    @endif
                </div>

                <div class="col-6">
                    <table class="table table-borderless fs-14 mb-0">
                        <tr>
                            <td class="text-muted">Subtotal:</td>
                            <td class="text-end fw-bold">{{ $invoice->currency_symbol }}{{ number_format($invoice->subtotal, 2) }}</td>
                        </tr>
                        @if($invoice->discount_amount > 0)
                        <tr>
                            <td class="text-muted">Discount:</td>
                            <td class="text-end text-success">-{{ $invoice->currency_symbol }}{{ number_format($invoice->discount_amount, 2) }}</td>
                        </tr>
                        @endif
                        @foreach($invoice->taxes as $itax)
                        <tr>
                            <td class="text-muted">{{ $itax->tax_name }} ({{ $itax->tax_rate }}%):</td>
                            <td class="text-end">+{{ $invoice->currency_symbol }}{{ number_format($itax->tax_amount, 2) }}</td>
                        </tr>
                        @endforeach
                        @if($invoice->additional_charges > 0)
                        <tr>
                            <td class="text-muted">Additional Charges:</td>
                            <td class="text-end">+{{ $invoice->currency_symbol }}{{ number_format($invoice->additional_charges, 2) }}</td>
                        </tr>
                        @endif
                        @if($invoice->round_off != 0)
                        <tr>
                            <td class="text-muted">Round Off:</td>
                            <td class="text-end">{{ $invoice->currency_symbol }}{{ number_format($invoice->round_off, 2) }}</td>
                        </tr>
                        @endif
                        <tr class="border-top border-2">
                            <td class="fw-bold fs-16 text-dark">Grand Total:</td>
                            <td class="text-end fw-bold fs-16 text-primary">{{ $invoice->currency_symbol }}{{ number_format($invoice->grand_total, 2) }}</td>
                        </tr>
                        <tr>
                            <td class="text-muted">Total Paid:</td>
                            <td class="text-end text-success fw-bold">{{ $invoice->currency_symbol }}{{ number_format($invoice->paid_amount, 2) }}</td>
                        </tr>
                        <tr class="table-light">
                            <td class="fw-bold fs-15 text-danger">Balance Due:</td>
                            <td class="text-end fw-bold fs-15 text-danger">{{ $invoice->currency_symbol }}{{ number_format($invoice->balance_due, 2) }}</td>
                        </tr>
                    </table>
                </div>
            </div>

            <!-- Notes & Footer -->
            @if($invoice->notes)
            <div class="border-top pt-3 mb-2">
                <small class="text-muted fw-semibold">NOTES:</small>
                <div class="fs-13 text-secondary">{{ $invoice->notes }}</div>
            </div>
            @endif

            @if($invoice->terms_conditions)
            <div class="border-top pt-2">
                <small class="text-muted fw-semibold">TERMS & CONDITIONS:</small>
                <div class="fs-12 text-muted">{{ $invoice->terms_conditions }}</div>
            </div>
            @endif
        </div>
    </div>

    <!-- Right Sidebar Panel: Online Payment Link & Payment Ledger -->
    <div class="col-12 col-lg-4 d-print-none">
        <!-- Online Payment Link Box -->
        @if($invoice->balance_due > 0 && $invoice->status !== 'Cancelled')
        <div class="card-custom p-4 mb-4">
            <h6 class="fw-bold border-bottom pb-2 mb-3"><i class="fa-solid fa-link text-primary me-2"></i> Online Payment Link</h6>
            
            @if($activeLink)
            <div class="mb-3">
                <label class="form-label fs-13 text-muted">Client Direct Checkout URL:</label>
                <div class="input-group">
                    <input type="text" class="form-control fs-13" id="payLinkInput" value="{{ url('/pay/invoice/' . $activeLink->token) }}" readonly>
                    <button class="btn btn-outline-primary" onclick="copyPayLink()"><i class="fa-regular fa-copy"></i></button>
                </div>
                <small class="text-muted d-block mt-1">Amount: <strong>${{ number_format($activeLink->amount, 2) }}</strong></small>
            </div>

            <div class="d-grid gap-2">
                <a href="{{ url('/pay/invoice/' . $activeLink->token) }}" target="_blank" class="btn btn-primary btn-sm">
                    <i class="fa-solid fa-up-right-from-square me-1"></i> Open Payment Page
                </a>
                <form action="{{ route('payments.generate_link', $invoice) }}" method="POST">
                    @csrf
                    <button type="submit" class="btn btn-light border btn-sm w-100"><i class="fa-solid fa-rotate me-1"></i> Regenerate Link</button>
                </form>
            </div>
            @else
            <form action="{{ route('payments.generate_link', $invoice) }}" method="POST">
                @csrf
                <button type="submit" class="btn btn-primary w-100"><i class="fa-solid fa-bolt me-1"></i> Generate Payment Link</button>
            </form>
            @endif
        </div>
        @endif

        <!-- Payments Applied -->
        <div class="card-custom p-4 mb-4">
            <h6 class="fw-bold border-bottom pb-2 mb-3"><i class="fa-solid fa-receipt text-success me-2"></i> Payments Recorded</h6>
            <ul class="list-unstyled mb-0 fs-14">
                @forelse($invoice->payments as $pay)
                <li class="d-flex justify-content-between border-bottom pb-2 mb-2">
                    <div>
                        <div class="fw-bold text-dark">{{ $pay->payment_id }}</div>
                        <small class="text-muted">{{ $pay->payment_date->format('M d, Y') }} &bull; {{ $pay->payment_method }}</small>
                    </div>
                    <div class="text-end fw-bold text-success">
                        +${{ number_format($pay->amount, 2) }}
                    </div>
                </li>
                @empty
                <li class="text-muted text-center py-2 fs-13">No payments recorded yet.</li>
                @endforelse
            </ul>
        </div>
    </div>
</div>

<!-- Cancel Invoice Modal -->
<div class="modal fade" id="cancelModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="{{ route('invoices.cancel', $invoice) }}" method="POST">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title fw-bold text-danger"><i class="fa-solid fa-ban me-2"></i> Cancel Invoice</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <p class="fs-14">Cancelling this invoice will mark it as Cancelled while preserving complete historical audit logs.</p>
                    <div class="mb-3">
                        <label class="form-label fs-14 fw-medium">Cancellation Reason <span class="text-danger">*</span></label>
                        <textarea name="cancellation_reason" class="form-control" rows="3" required placeholder="e.g. Scope change or duplicate invoice..."></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light border" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-danger">Confirm Cancellation</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    function copyPayLink() {
        const input = document.getElementById('payLinkInput');
        input.select();
        document.execCommand('copy');
        alert('Payment link copied to clipboard!');
    }
</script>
@endpush
