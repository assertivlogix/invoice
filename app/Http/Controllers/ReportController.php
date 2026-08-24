<?php

namespace App\Http\Controllers;

use App\Models\Client;
use App\Models\CompanySetting;
use App\Models\Invoice;
use App\Models\InvoiceTax;
use App\Models\Payment;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;

class ReportController extends Controller
{
    public static function getFinancialYears(): array
    {
        $currentYear = (int) date('Y');
        $years = [];
        for ($i = $currentYear + 1; $i >= $currentYear - 4; $i--) {
            $prev = $i - 1;
            $next = $i;
            $years["{$prev}-{$next}"] = "FY {$prev}-" . substr($next, -2) . " (Apr {$prev} - Mar {$next})";
        }
        return $years;
    }

    protected function getFinancialYearDates(?string $fy): ?array
    {
        if (!$fy) {
            return null;
        }

        if (preg_match('/^(\d{4})-(\d{4})$/', $fy, $matches)) {
            return [
                'from' => "{$matches[1]}-04-01",
                'to' => "{$matches[2]}-03-31",
            ];
        }

        if (preg_match('/^\d{4}$/', $fy)) {
            return [
                'from' => "{$fy}-01-01",
                'to' => "{$fy}-12-31",
            ];
        }

        return null;
    }

    public function index(Request $request)
    {
        $financialYears = self::getFinancialYears();
        $selectedFy = $request->get('financial_year');
        return view('reports.index', compact('financialYears', 'selectedFy'));
    }

    public function invoices(Request $request)
    {
        $query = Invoice::with('client');

        $dateFrom = $request->date_from;
        $dateTo = $request->date_to;

        if ($request->filled('financial_year')) {
            $fyRange = $this->getFinancialYearDates($request->financial_year);
            if ($fyRange) {
                $dateFrom = $fyRange['from'];
                $dateTo = $fyRange['to'];
            }
        }

        if ($dateFrom) {
            $query->where('invoice_date', '>=', $dateFrom);
        }
        if ($dateTo) {
            $query->where('invoice_date', '<=', $dateTo);
        }
        if ($request->filled('client_id')) {
            $query->where('client_id', $request->client_id);
        }
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $summary = [
            'count' => (clone $query)->count(),
            'total' => (clone $query)->sum('grand_total'),
            'paid' => (clone $query)->sum('paid_amount'),
            'balance' => (clone $query)->sum('balance_due'),
        ];

        if ($request->get('export') === 'csv') {
            $invoices = $query->get();
            $csvHeader = ['Invoice Number', 'Client', 'Date', 'Due Date', 'Total', 'Paid', 'Balance', 'Status'];
            $csvData = [];

            foreach ($invoices as $inv) {
                $csvData[] = [
                    $inv->invoice_number,
                    $inv->client->company_name,
                    $inv->invoice_date->format('Y-m-d'),
                    $inv->due_date->format('Y-m-d'),
                    $inv->grand_total,
                    $inv->paid_amount,
                    $inv->balance_due,
                    $inv->status,
                ];
            }

            $fyLabel = $request->filled('financial_year') ? '_' . $request->financial_year : '';
            return $this->downloadCsv('invoice_report' . $fyLabel . '_' . date('Y-m-d') . '.csv', $csvHeader, $csvData);
        }

        if ($request->get('export') === 'pdf') {
            $invoices = $query->get();
            $company = CompanySetting::getSettings();
            $financialYears = self::getFinancialYears();
            $fyTitle = $request->filled('financial_year') && isset($financialYears[$request->financial_year]) 
                ? $financialYears[$request->financial_year] 
                : ($dateFrom ? "Period: {$dateFrom} to {$dateTo}" : "All Time");

            $pdf = Pdf::loadView('pdf.reports.invoices', compact('invoices', 'summary', 'company', 'fyTitle', 'dateFrom', 'dateTo'));
            $pdf->setPaper('A4', 'landscape');
            
            $fyLabel = $request->filled('financial_year') ? '_' . $request->financial_year : '';
            return $pdf->download('Invoice_Report' . $fyLabel . '.pdf');
        }

        $invoices = $query->paginate(20)->withQueryString();
        $clients = Client::orderBy('company_name')->get();
        $financialYears = self::getFinancialYears();

        return view('reports.invoices', compact('invoices', 'clients', 'summary', 'financialYears', 'dateFrom', 'dateTo'));
    }

    public function payments(Request $request)
    {
        $query = Payment::with(['client', 'invoice']);

        $dateFrom = $request->date_from;
        $dateTo = $request->date_to;

        if ($request->filled('financial_year')) {
            $fyRange = $this->getFinancialYearDates($request->financial_year);
            if ($fyRange) {
                $dateFrom = $fyRange['from'];
                $dateTo = $fyRange['to'];
            }
        }

        if ($dateFrom) {
            $query->where('payment_date', '>=', $dateFrom);
        }
        if ($dateTo) {
            $query->where('payment_date', '<=', $dateTo);
        }
        if ($request->filled('client_id')) {
            $query->where('client_id', $request->client_id);
        }
        if ($request->filled('payment_method')) {
            $query->where('payment_method', $request->payment_method);
        }

        $totalAmount = (clone $query)->sum('amount');

        if ($request->get('export') === 'csv') {
            $payments = $query->get();
            $csvHeader = ['Payment ID', 'Client', 'Invoice Number', 'Payment Date', 'Amount', 'Payment Method', 'Transaction ID'];
            $csvData = [];

            foreach ($payments as $p) {
                $csvData[] = [
                    $p->payment_id,
                    $p->client->company_name,
                    $p->invoice ? $p->invoice->invoice_number : 'N/A',
                    $p->payment_date->format('Y-m-d'),
                    $p->amount,
                    $p->payment_method,
                    $p->transaction_id ?? '',
                ];
            }

            $fyLabel = $request->filled('financial_year') ? '_' . $request->financial_year : '';
            return $this->downloadCsv('payment_report' . $fyLabel . '_' . date('Y-m-d') . '.csv', $csvHeader, $csvData);
        }

        if ($request->get('export') === 'pdf') {
            $payments = $query->get();
            $company = CompanySetting::getSettings();
            $financialYears = self::getFinancialYears();
            $fyTitle = $request->filled('financial_year') && isset($financialYears[$request->financial_year]) 
                ? $financialYears[$request->financial_year] 
                : ($dateFrom ? "Period: {$dateFrom} to {$dateTo}" : "All Time");

            $pdf = Pdf::loadView('pdf.reports.payments', compact('payments', 'totalAmount', 'company', 'fyTitle', 'dateFrom', 'dateTo'));
            $pdf->setPaper('A4', 'landscape');
            
            $fyLabel = $request->filled('financial_year') ? '_' . $request->financial_year : '';
            return $pdf->download('Payment_Report' . $fyLabel . '.pdf');
        }

        $payments = $query->paginate(20)->withQueryString();
        $clients = Client::orderBy('company_name')->get();
        $financialYears = self::getFinancialYears();

        $methodBreakdown = Payment::selectRaw('payment_method, COUNT(*) as count, SUM(amount) as total')
            ->groupBy('payment_method')
            ->get();

        return view('reports.payments', compact('payments', 'clients', 'totalAmount', 'methodBreakdown', 'financialYears', 'dateFrom', 'dateTo'));
    }

    public function revenue(Request $request)
    {
        $dateFrom = $request->date_from;
        $dateTo = $request->date_to;

        if ($request->filled('financial_year')) {
            $fyRange = $this->getFinancialYearDates($request->financial_year);
            if ($fyRange) {
                $dateFrom = $fyRange['from'];
                $dateTo = $fyRange['to'];
            }
        }

        $clients = Client::with(['invoices' => function($q) use ($dateFrom, $dateTo) {
            if ($dateFrom) $q->where('invoice_date', '>=', $dateFrom);
            if ($dateTo) $q->where('invoice_date', '<=', $dateTo);
        }])->get()->map(function ($client) {
            $totalInvoices = $client->invoices->count();
            $totalRevenue = $client->invoices->where('status', '!=', 'Cancelled')->sum('grand_total');
            $paid = $client->invoices->where('status', '!=', 'Cancelled')->sum('paid_amount');
            $outstanding = $client->invoices->where('status', '!=', 'Cancelled')->sum('balance_due');

            return [
                'client_id' => $client->client_id,
                'company_name' => $client->company_name,
                'total_invoices' => $totalInvoices,
                'total_revenue' => $totalRevenue,
                'paid' => $paid,
                'outstanding' => $outstanding,
            ];
        });

        if ($request->get('export') === 'csv') {
            $csvHeader = ['Client ID', 'Client Name', 'Total Invoices', 'Total Revenue', 'Paid Amount', 'Outstanding Balance'];
            $csvData = [];

            foreach ($clients as $c) {
                $csvData[] = [
                    $c['client_id'],
                    $c['company_name'],
                    $c['total_invoices'],
                    $c['total_revenue'],
                    $c['paid'],
                    $c['outstanding'],
                ];
            }

            $fyLabel = $request->filled('financial_year') ? '_' . $request->financial_year : '';
            return $this->downloadCsv('client_revenue_report' . $fyLabel . '_' . date('Y-m-d') . '.csv', $csvHeader, $csvData);
        }

        if ($request->get('export') === 'pdf') {
            $company = CompanySetting::getSettings();
            $financialYears = self::getFinancialYears();
            $fyTitle = $request->filled('financial_year') && isset($financialYears[$request->financial_year]) 
                ? $financialYears[$request->financial_year] 
                : ($dateFrom ? "Period: {$dateFrom} to {$dateTo}" : "All Time");

            $pdf = Pdf::loadView('pdf.reports.revenue', compact('clients', 'company', 'fyTitle', 'dateFrom', 'dateTo'));
            $pdf->setPaper('A4', 'portrait');

            $fyLabel = $request->filled('financial_year') ? '_' . $request->financial_year : '';
            return $pdf->download('Client_Revenue_Report' . $fyLabel . '.pdf');
        }

        $financialYears = self::getFinancialYears();

        return view('reports.revenue', compact('clients', 'financialYears', 'dateFrom', 'dateTo'));
    }

    public function tax(Request $request)
    {
        $query = InvoiceTax::with(['invoice.client']);

        $dateFrom = $request->date_from;
        $dateTo = $request->date_to;

        if ($request->filled('financial_year')) {
            $fyRange = $this->getFinancialYearDates($request->financial_year);
            if ($fyRange) {
                $dateFrom = $fyRange['from'];
                $dateTo = $fyRange['to'];
            }
        }

        if ($dateFrom || $dateTo) {
            $query->whereHas('invoice', function ($q) use ($dateFrom, $dateTo) {
                if ($dateFrom) $q->where('invoice_date', '>=', $dateFrom);
                if ($dateTo) $q->where('invoice_date', '<=', $dateTo);
            });
        }

        $taxRecords = $query->latest('id')->get();

        $taxSummaryQuery = InvoiceTax::query();
        if ($dateFrom || $dateTo) {
            $taxSummaryQuery->whereHas('invoice', function ($q) use ($dateFrom, $dateTo) {
                if ($dateFrom) $q->where('invoice_date', '>=', $dateFrom);
                if ($dateTo) $q->where('invoice_date', '<=', $dateTo);
            });
        }

        $taxSummary = $taxSummaryQuery->selectRaw('tax_name, tax_rate, SUM(tax_amount) as total_tax')
            ->groupBy('tax_name', 'tax_rate')
            ->get();

        if ($request->get('export') === 'csv') {
            $csvHeader = ['Tax Name', 'Rate (%)', 'Tax Amount', 'Invoice Number', 'Client'];
            $csvData = [];

            foreach ($taxRecords as $tr) {
                $csvData[] = [
                    $tr->tax_name,
                    $tr->tax_rate,
                    $tr->tax_amount,
                    $tr->invoice ? $tr->invoice->invoice_number : '',
                    $tr->invoice && $tr->invoice->client ? $tr->invoice->client->company_name : '',
                ];
            }

            $fyLabel = $request->filled('financial_year') ? '_' . $request->financial_year : '';
            return $this->downloadCsv('tax_report' . $fyLabel . '_' . date('Y-m-d') . '.csv', $csvHeader, $csvData);
        }

        if ($request->get('export') === 'pdf') {
            $company = CompanySetting::getSettings();
            $financialYears = self::getFinancialYears();
            $fyTitle = $request->filled('financial_year') && isset($financialYears[$request->financial_year]) 
                ? $financialYears[$request->financial_year] 
                : ($dateFrom ? "Period: {$dateFrom} to {$dateTo}" : "All Time");

            $pdf = Pdf::loadView('pdf.reports.tax', compact('taxRecords', 'taxSummary', 'company', 'fyTitle', 'dateFrom', 'dateTo'));
            $pdf->setPaper('A4', 'portrait');

            $fyLabel = $request->filled('financial_year') ? '_' . $request->financial_year : '';
            return $pdf->download('Tax_Report' . $fyLabel . '.pdf');
        }

        $financialYears = self::getFinancialYears();

        return view('reports.tax', compact('taxRecords', 'taxSummary', 'financialYears', 'dateFrom', 'dateTo'));
    }

    protected function downloadCsv(string $filename, array $header, array $rows)
    {
        $callback = function () use ($header, $rows) {
            $file = fopen('php://output', 'w');
            fputcsv($file, $header);
            foreach ($rows as $row) {
                fputcsv($file, $row);
            }
            fclose($file);
        };

        return response()->stream($callback, 200, [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ]);
    }
}
