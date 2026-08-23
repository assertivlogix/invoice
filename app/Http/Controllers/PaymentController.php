<?php

namespace App\Http\Controllers;

use App\Models\Client;
use App\Models\Invoice;
use App\Models\Payment;
use App\Services\InvoiceService;
use App\Services\PaymentService;
use Illuminate\Http\Request;

class PaymentController extends Controller
{
    protected PaymentService $paymentService;
    protected InvoiceService $invoiceService;

    public function __construct(PaymentService $paymentService, InvoiceService $invoiceService)
    {
        $this->paymentService = $paymentService;
        $this->invoiceService = $invoiceService;
    }

    public function index(Request $request)
    {
        $query = Payment::with(['invoice', 'client', 'createdBy']);

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('payment_id', 'like', "%{$search}%")
                  ->orWhere('transaction_id', 'like', "%{$search}%")
                  ->orWhere('reference_number', 'like', "%{$search}%");
            });
        }

        if ($request->filled('client_id')) {
            $query->where('client_id', $request->client_id);
        }

        if ($request->filled('payment_method')) {
            $query->where('payment_method', $request->payment_method);
        }

        if ($request->filled('date_from')) {
            $query->where('payment_date', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->where('payment_date', '<=', $request->date_to);
        }

        $payments = $query->latest('payment_date')->latest('id')->paginate(15)->withQueryString();
        $clients = Client::orderBy('company_name')->get();

        return view('payments.index', compact('payments', 'clients'));
    }

    public function create(Request $request)
    {
        $invoices = Invoice::whereNotIn('status', ['Paid', 'Cancelled'])
            ->where('balance_due', '>', 0)
            ->with('client')
            ->get();

        $selectedInvoiceId = $request->get('invoice_id');
        $selectedInvoice = $selectedInvoiceId ? Invoice::find($selectedInvoiceId) : null;

        $suggestedPaymentId = $this->paymentService->generatePaymentId();

        return view('payments.create', compact('invoices', 'selectedInvoice', 'suggestedPaymentId'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'invoice_id' => 'required|exists:invoices,id',
            'payment_date' => 'required|date',
            'amount' => 'required|numeric|min:0.01',
            'payment_method' => 'required|string',
            'transaction_id' => 'nullable|string|max:100',
            'reference_number' => 'nullable|string|max:100',
            'notes' => 'nullable|string',
        ]);

        $payment = $this->paymentService->recordPayment($validated);

        return redirect()->route('payments.index')->with('success', "Payment {$payment->payment_id} recorded successfully.");
    }

    public function generateLink(Invoice $invoice)
    {
        $link = $this->invoiceService->createPaymentLink($invoice);
        return back()->with('success', "New payment link generated: " . url("/pay/invoice/{$link->token}"));
    }
}
