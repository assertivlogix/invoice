<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use App\Models\Client;
use App\Models\Invoice;
use App\Models\Payment;
use App\Models\Project;
use App\Services\InvoiceService;
use Carbon\Carbon;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index(InvoiceService $invoiceService)
    {
        // Update overdue status on page load
        $invoiceService->updateOverdueStatuses();

        $totalClients = Client::where('status', 'active')->count();
        $totalInvoiced = Invoice::where('status', '!=', 'Cancelled')->sum('grand_total');
        $totalPaid = Payment::where('status', 'Completed')->sum('amount');
        $totalOutstanding = Invoice::whereNotIn('status', ['Paid', 'Cancelled'])->sum('balance_due');
        $totalOverdue = Invoice::where('status', 'Overdue')->sum('balance_due');
        $thisMonthInvoices = Invoice::whereYear('invoice_date', date('Y'))
            ->whereMonth('invoice_date', date('m'))
            ->count();

        // Revenue Chart Data (Last 6 Months)
        $months = [];
        $invoicedData = [];
        $paidData = [];

        for ($i = 5; $i >= 0; $i--) {
            $date = Carbon::now()->subMonths($i);
            $monthLabel = $date->format('M Y');
            $months[] = $monthLabel;

            $mInvoiced = Invoice::whereYear('invoice_date', $date->year)
                ->whereMonth('invoice_date', $date->month)
                ->where('status', '!=', 'Cancelled')
                ->sum('grand_total');

            $mPaid = Payment::whereYear('payment_date', $date->year)
                ->whereMonth('payment_date', $date->month)
                ->where('status', 'Completed')
                ->sum('amount');

            $invoicedData[] = round((float)$mInvoiced, 2);
            $paidData[] = round((float)$mPaid, 2);
        }

        // Status Breakdown
        $statusCounts = [
            'Draft' => Invoice::where('status', 'Draft')->count(),
            'Sent' => Invoice::where('status', 'Sent')->count(),
            'Partially Paid' => Invoice::where('status', 'Partially Paid')->count(),
            'Paid' => Invoice::where('status', 'Paid')->count(),
            'Overdue' => Invoice::where('status', 'Overdue')->count(),
            'Cancelled' => Invoice::where('status', 'Cancelled')->count(),
        ];

        // Top 5 Clients by Revenue
        $topClients = Client::withCount('invoices')
            ->get()
            ->sortByDesc(fn($c) => $c->total_invoiced)
            ->take(5);

        // Recent Activity
        $recentInvoices = Invoice::with('client')->latest()->take(5)->get();
        $recentPayments = Payment::with(['client', 'invoice'])->latest()->take(5)->get();
        $recentActivities = ActivityLog::with('user')->latest()->take(10)->get();

        return view('dashboard.index', compact(
            'totalClients',
            'totalInvoiced',
            'totalPaid',
            'totalOutstanding',
            'totalOverdue',
            'thisMonthInvoices',
            'months',
            'invoicedData',
            'paidData',
            'statusCounts',
            'topClients',
            'recentInvoices',
            'recentPayments',
            'recentActivities'
        ));
    }
}
