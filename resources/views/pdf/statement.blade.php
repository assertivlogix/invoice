<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Statement of Account - {{ $statement['client']->company_name }}</title>
    <style>
        body { font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif; font-size: 12px; color: #333333; line-height: 1.4; margin: 0; padding: 20px; }
        .header-table { width: 100%; margin-bottom: 25px; border-bottom: 2px solid #2563eb; padding-bottom: 15px; }
        .company-name { font-size: 18px; font-weight: bold; color: #2563eb; margin-bottom: 3px; }
        .statement-title { font-size: 22px; font-weight: bold; color: #111827; text-align: right; }
        .summary-box { background: #f8fafc; padding: 12px; border-radius: 6px; border: 1px solid #e2e8f0; margin-bottom: 25px; }
        .ledger-table { width: 100%; border-collapse: collapse; margin-bottom: 25px; }
        .ledger-table th { background: #0f172a; color: #ffffff; padding: 8px 10px; font-size: 11px; text-transform: uppercase; text-align: left; }
        .ledger-table td { padding: 8px 10px; border-bottom: 1px solid #e2e8f0; font-size: 11px; }
        .opening-row td { background: #f1f5f9; font-weight: bold; }
        .totals-row td { background: #f8fafc; font-weight: bold; font-size: 12px; border-top: 2px solid #0f172a; border-bottom: 2px solid #0f172a; }
        .badge-invoice { color: #2563eb; font-weight: bold; }
        .badge-payment { color: #16a34a; font-weight: bold; }
        .text-end { text-align: right; }
        .text-center { text-align: center; }
        .text-muted { color: #64748b; }
        .text-danger { color: #dc2626; }
        .text-success { color: #16a34a; }
        .text-primary { color: #2563eb; }
        .footer { margin-top: 40px; border-top: 1px solid #e2e8f0; padding-top: 12px; text-align: center; font-size: 10px; color: #64748b; }
    </style>
</head>
<body>

<table class="header-table">
    <tr>
        <td style="width: 50%; vertical-align: top;">
            <div class="company-name">{{ $company->company_name }}</div>
            <div style="font-size: 11px; color: #64748b;">
                {{ $company->address }}<br>
                {{ $company->city }}, {{ $company->state }} {{ $company->zip_code }} {{ $company->country }}<br>
                Email: {{ $company->email }} | Phone: {{ $company->phone }}
            </div>
        </td>
        <td style="width: 50%; text-align: right; vertical-align: top;">
            <div class="statement-title">STATEMENT OF ACCOUNT</div>
            <div style="font-size: 11px; color: #64748b; margin-top: 4px;">
                <strong>Statement Period:</strong> {{ \Carbon\Carbon::parse($statement['startDate'])->format('M d, Y') }} — {{ \Carbon\Carbon::parse($statement['endDate'])->format('M d, Y') }}<br>
                <strong>Date Generated:</strong> {{ date('M d, Y') }}
            </div>
        </td>
    </tr>
</table>

<div class="summary-box">
    <table style="width: 100%;">
        <tr>
            <td style="width: 55%; vertical-align: top;">
                <div style="font-size: 10px; text-transform: uppercase; color: #64748b; font-weight: bold;">Statement For:</div>
                <div style="font-size: 14px; font-weight: bold; color: #111827; margin-top: 2px;">{{ $statement['client']->company_name }}</div>
                <div style="font-size: 11px; color: #475569;">{{ $statement['client']->formatted_address }}</div>
                <div style="font-size: 11px; color: #475569;">Email: {{ $statement['client']->email }}</div>
            </td>
            <td style="width: 45%; text-align: right; vertical-align: top;">
                <div style="font-size: 10px; text-transform: uppercase; color: #64748b; font-weight: bold;">Account Summary</div>
                <div style="font-size: 11px; margin-top: 2px;">Opening Balance: <strong>${{ number_format($statement['openingBalance'], 2) }}</strong></div>
                <div style="font-size: 11px;" class="text-primary">Total Invoiced: ${{ number_format($statement['totalDebit'], 2) }}</div>
                <div style="font-size: 11px;" class="text-success">Total Received: ${{ number_format($statement['totalCredit'], 2) }}</div>
                <div style="font-size: 13px; font-weight: bold; margin-top: 4px;" class="text-danger">Closing Balance: ${{ number_format($statement['closingBalance'], 2) }}</div>
            </td>
        </tr>
    </table>
</div>

<table class="ledger-table">
    <thead>
        <tr>
            <th style="width: 12%;">Date</th>
            <th style="width: 14%;">Type</th>
            <th style="width: 18%;">Reference #</th>
            <th style="width: 26%;">Description</th>
            <th style="width: 10%; text-align: right;">Debit</th>
            <th style="width: 10%; text-align: right;">Credit</th>
            <th style="width: 10%; text-align: right;">Balance</th>
        </tr>
    </thead>
    <tbody>
        <tr class="opening-row">
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
                <span class="{{ $row['type'] === 'Invoice' ? 'badge-invoice' : 'badge-payment' }}">
                    {{ $row['type'] }}
                </span>
            </td>
            <td style="font-weight: bold;">{{ $row['reference'] }}</td>
            <td>{{ $row['description'] }}</td>
            <td class="text-end">{{ $row['debit'] > 0 ? '$' . number_format($row['debit'], 2) : '-' }}</td>
            <td class="text-end text-success">{{ $row['credit'] > 0 ? '$' . number_format($row['credit'], 2) : '-' }}</td>
            <td class="text-end" style="font-weight: bold;">${{ number_format($row['balance'], 2) }}</td>
        </tr>
        @empty
        <tr>
            <td colspan="7" class="text-center text-muted" style="padding: 15px;">No transactions during this statement period.</td>
        </tr>
        @endforelse
    </tbody>
    <tfoot>
        <tr class="totals-row">
            <td colspan="4" class="text-end">Totals:</td>
            <td class="text-end text-primary">${{ number_format($statement['totalDebit'], 2) }}</td>
            <td class="text-end text-success">${{ number_format($statement['totalCredit'], 2) }}</td>
            <td class="text-end text-danger">${{ number_format($statement['closingBalance'], 2) }}</td>
        </tr>
    </tfoot>
</table>

<div class="footer">
    If you have any questions regarding this statement, please contact {{ $company->email }}.<br>
    Thank you for your business — {{ $company->company_name }}
</div>

</body>
</html>
