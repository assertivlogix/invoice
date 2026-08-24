@extends('layouts.app')

@section('title', 'Client Statement — ' . $statement['client']->company_name)
@section('page-title', 'Client Account Statement')

@section('content')
<div class="d-flex align-items-center justify-content-between mb-4 d-print-none">
    <div>
        <a href="{{ route('clients.show', $statement['client']) }}" class="text-decoration-none text-muted"><i class="fa-solid fa-arrow-left me-1"></i> Back to Client Dashboard</a>
        <h4 class="fw-bold mt-2">Account Statement</h4>
    </div>
    <div class="d-flex gap-2">
        <a href="{{ route('clients.statement_pdf', [$statement['client'], 'start_date' => $statement['startDate'], 'end_date' => $statement['endDate']]) }}" class="btn btn-outline-danger">
            <i class="fa-solid fa-file-pdf me-1"></i> Download PDF
        </a>
        <button onclick="window.print();" class="btn btn-outline-secondary"><i class="fa-solid fa-print me-1"></i> Print Statement</button>
    </div>
</div>

<!-- Date Filter Form -->
<div class="card-custom p-3 mb-4 d-print-none">
    <form action="{{ route('clients.statement', $statement['client']) }}" method="GET" class="row g-3 align-items-end">
        <div class="col-md-4">
            <label class="form-label fs-14 fw-medium">Start Date</label>
            <input type="date" name="start_date" class="form-control" value="{{ $statement['startDate'] }}">
        </div>
        <div class="col-md-4">
            <label class="form-label fs-14 fw-medium">End Date</label>
            <input type="date" name="end_date" class="form-control" value="{{ $statement['endDate'] }}">
        </div>
        <div class="col-md-4">
            <button type="submit" class="btn btn-primary w-100"><i class="fa-solid fa-filter me-1"></i> Generate Statement</button>
        </div>
    </form>
</div>

<!-- Statement Paper View -->
<div class="card-custom p-5 bg-white">
    <div class="row border-bottom pb-4 mb-4">
        <div class="col-6">
            <h4 class="fw-bold text-primary mb-1">ASSERTIV LOGIX</h4>
            <div class="text-muted fs-14">
                100 Innovation Way, Suite 400<br>
                San Francisco, CA 94016<br>
                Email: info@assertivlogix.com
            </div>
        </div>
        <div class="col-6 text-end">
            <h3 class="fw-bold text-dark mb-1">STATEMENT OF ACCOUNT</h3>
            <div class="text-muted fs-14">
                <strong>Statement Period:</strong> {{ \Carbon\Carbon::parse($statement['startDate'])->format('M d, Y') }} — {{ \Carbon\Carbon::parse($statement['endDate'])->format('M d, Y') }}<br>
                <strong>Date Generated:</strong> {{ date('M d, Y') }}
            </div>
        </div>
    </div>

    <!-- Client Info Box -->
    <div class="bg-light p-3 rounded-3 mb-4">
        <div class="row">
            <div class="col-6">
                <small class="text-muted text-uppercase fw-semibold fs-12">Statement For:</small>
                <h5 class="fw-bold mb-1 text-dark">{{ $statement['client']->company_name }}</h5>
                <div class="fs-14 text-secondary">{{ $statement['client']->formatted_address }}</div>
                <div class="fs-14 text-secondary">Email: {{ $statement['client']->email }}</div>
            </div>
            <div class="col-6 text-end d-flex flex-column justify-content-center">
                <small class="text-muted text-uppercase fw-semibold fs-12">Account Summary</small>
                <div class="fs-14">Opening Balance: <strong>${{ number_format($statement['openingBalance'], 2) }}</strong></div>
                <div class="fs-14 text-primary fw-semibold">Total Invoiced: ${{ number_format($statement['totalDebit'], 2) }}</div>
                <div class="fs-14 text-success fw-semibold">Total Received: ${{ number_format($statement['totalCredit'], 2) }}</div>
                <div class="fs-16 fw-bold text-danger mt-1">Closing Balance: ${{ number_format($statement['closingBalance'], 2) }}</div>
            </div>
        </div>
    </div>

    <!-- Ledger Table -->
    <div class="table-responsive mb-4">
        <table class="table table-bordered align-middle fs-14">
            <thead class="table-light">
                <tr>
                    <th>Date</th>
                    <th>Transaction Type</th>
                    <th>Reference #</th>
                    <th>Description</th>
                    <th class="text-end">Debit (Invoiced)</th>
                    <th class="text-end">Credit (Paid)</th>
                    <th class="text-end">Balance Due</th>
                </tr>
            </thead>
            <tbody>
                <tr class="table-secondary fw-semibold">
                    <td>{{ \Carbon\Carbon::parse($statement['startDate'])->format('Y-m-d') }}</td>
                    <td colspan="3">Opening Balance</td>
                    <td class="text-end">-</td>
                    <td class="text-end">-</td>
                    <td class="text-end">${{ number_format($statement['openingBalance'], 2) }}</td>
                </tr>
                @forelse($statement['ledger'] as $row)
                <tr>
                    <td>{{ $row['date'] }}</td>
                    <td>
                        <span class="badge {{ $row['type'] === 'Invoice' ? 'bg-primary-subtle text-primary' : 'bg-success-subtle text-success' }}">
                            {{ $row['type'] }}
                        </span>
                    </td>
                    <td class="fw-semibold">{{ $row['reference'] }}</td>
                    <td>{{ $row['description'] }}</td>
                    <td class="text-end font-monospace">{{ $row['debit'] > 0 ? '$' . number_format($row['debit'], 2) : '-' }}</td>
                    <td class="text-end font-monospace text-success">{{ $row['credit'] > 0 ? '$' . number_format($row['credit'], 2) : '-' }}</td>
                    <td class="text-end font-monospace fw-bold">${{ number_format($row['balance'], 2) }}</td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" class="text-center text-muted py-3">No transactions during this statement period.</td>
                </tr>
                @endforelse
            </tbody>
            <tfoot>
                <tr class="table-light fw-bold fs-15">
                    <td colspan="4" class="text-end">Totals:</td>
                    <td class="text-end text-primary">${{ number_format($statement['totalDebit'], 2) }}</td>
                    <td class="text-end text-success">${{ number_format($statement['totalCredit'], 2) }}</td>
                    <td class="text-end text-danger">${{ number_format($statement['closingBalance'], 2) }}</td>
                </tr>
            </tfoot>
        </table>
    </div>

    <div class="text-muted fs-13 text-center border-top pt-3">
        If you have any questions regarding this statement, please contact info@assertivlogix.com.
    </div>
</div>
@endsection
