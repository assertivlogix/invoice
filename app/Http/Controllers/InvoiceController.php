<?php

namespace App\Http\Controllers;

use App\Models\Client;
use App\Models\CompanySetting;
use App\Models\Currency;
use App\Models\Invoice;
use App\Models\Project;
use App\Models\Service;
use App\Models\Tax;
use App\Services\EmailInvoiceService;
use App\Services\InvoiceService;
use App\Services\PdfInvoiceService;
use Illuminate\Http\Request;

class InvoiceController extends Controller
{
    protected InvoiceService $invoiceService;
    protected PdfInvoiceService $pdfService;
    protected EmailInvoiceService $emailService;

    public function __construct(
        InvoiceService $invoiceService,
        PdfInvoiceService $pdfService,
        EmailInvoiceService $emailService
    ) {
        $this->invoiceService = $invoiceService;
        $this->pdfService = $pdfService;
        $this->emailService = $emailService;
    }

    public function index(Request $request)
    {
        $query = Invoice::with(['client', 'project']);

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('invoice_number', 'like', "%{$search}%")
                  ->orWhere('reference_number', 'like', "%{$search}%")
                  ->orWhereHas('client', function ($cq) use ($search) {
                      $cq->where('company_name', 'like', "%{$search}%")
                         ->orWhere('email', 'like', "%{$search}%");
                  });
            });
        }

        if ($request->filled('client_id')) {
            $query->where('client_id', $request->client_id);
        }

        if ($request->filled('project_id')) {
            $query->where('project_id', $request->project_id);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('currency')) {
            $query->where('currency', $request->currency);
        }

        if ($request->filled('date_from')) {
            $query->where('invoice_date', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->where('invoice_date', '<=', $request->date_to);
        }

        $sort = $request->get('sort', 'newest');
        switch ($sort) {
            case 'oldest':
                $query->orderBy('invoice_date', 'asc');
                break;
            case 'highest':
                $query->orderBy('grand_total', 'desc');
                break;
            case 'lowest':
                $query->orderBy('grand_total', 'asc');
                break;
            case 'due_date':
                $query->orderBy('due_date', 'asc');
                break;
            default:
                $query->orderBy('invoice_date', 'desc')->orderBy('id', 'desc');
                break;
        }

        $invoices = $query->paginate(15)->withQueryString();
        $clients = Client::orderBy('company_name')->get();
        $projects = Project::orderBy('project_name')->get();

        return view('invoices.index', compact('invoices', 'clients', 'projects'));
    }

    public function create(Request $request)
    {
        $suggestedNumber = $this->invoiceService->generateInvoiceNumber();
        $clients = Client::where('status', 'active')->orderBy('company_name')->get();
        $services = Service::where('status', 'active')->orderBy('service_name')->get();
        $taxes = Tax::where('is_active', true)->get();
        $currencies = Currency::all();
        $company = CompanySetting::getSettings();

        $selectedClientId = $request->get('client_id');
        $selectedClient = $selectedClientId ? Client::find($selectedClientId) : null;
        $projects = $selectedClient ? $selectedClient->projects : collect();

        return view('invoices.create', compact(
            'suggestedNumber',
            'clients',
            'services',
            'taxes',
            'currencies',
            'company',
            'selectedClient',
            'projects'
        ));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'client_id' => 'required|exists:clients,id',
            'project_id' => 'nullable|exists:projects,id',
            'invoice_number' => 'required|string|unique:invoices,invoice_number',
            'invoice_date' => 'required|date',
            'due_date' => 'required|date|after_or_equal:invoice_date',
            'currency' => 'required|string|max:10',
            'payment_terms' => 'required|string',
            'reference_number' => 'nullable|string|max:100',
            'po_number' => 'nullable|string|max:100',
            'template' => 'required|in:modern,corporate',
            'notes' => 'nullable|string',
            'terms_conditions' => 'nullable|string',
            'discount_type' => 'required|in:fixed,percentage',
            'discount_value' => 'required|numeric|min:0',
            'additional_charges' => 'nullable|numeric|min:0',
            'round_off' => 'nullable|numeric',
            'paid_amount' => 'nullable|numeric|min:0',
            'selected_taxes' => 'nullable|array',
            'items' => 'required|array|min:1',
            'items.*.item_name' => 'required|string|max:255',
            'items.*.service_id' => 'nullable|exists:services,id',
            'items.*.quantity' => 'required|numeric|min:0.01',
            'items.*.unit' => 'required|string',
            'items.*.unit_price' => 'required|numeric|min:0',
            'items.*.discount_type' => 'nullable|in:fixed,percentage',
            'items.*.discount_value' => 'nullable|numeric|min:0',
            'items.*.tax_rate' => 'nullable|numeric|min:0',
        ]);

        $invoice = $this->invoiceService->createInvoice($validated, $validated['items']);

        // Auto send email if marked as Sent
        if ($request->has('send_email') && $request->send_email == '1') {
            $pdfPath = $this->pdfService->generatePdf($invoice);
            $this->emailService->sendInvoiceEmail($invoice, $pdfPath);
        }

        return redirect()->route('invoices.show', $invoice)->with('success', 'Invoice created successfully.');
    }

    public function show(Invoice $invoice)
    {
        $invoice->load(['client', 'project', 'items.service', 'taxes', 'payments.createdBy', 'paymentLinks', 'reminders']);
        $company = CompanySetting::getSettings();
        
        $activeLink = $invoice->paymentLinks()->where('status', 'Active')->first();

        return view('invoices.show', compact('invoice', 'company', 'activeLink'));
    }

    public function edit(Invoice $invoice)
    {
        $invoice->load(['items', 'taxes']);
        $clients = Client::orderBy('company_name')->get();
        $projects = Project::where('client_id', $invoice->client_id)->get();
        $services = Service::where('status', 'active')->orderBy('service_name')->get();
        $taxes = Tax::where('is_active', true)->get();
        $currencies = Currency::all();
        $company = CompanySetting::getSettings();

        return view('invoices.edit', compact(
            'invoice',
            'clients',
            'projects',
            'services',
            'taxes',
            'currencies',
            'company'
        ));
    }

    public function update(Request $request, Invoice $invoice)
    {
        $validated = $request->validate([
            'client_id' => 'required|exists:clients,id',
            'project_id' => 'nullable|exists:projects,id',
            'invoice_date' => 'required|date',
            'due_date' => 'required|date|after_or_equal:invoice_date',
            'currency' => 'required|string|max:10',
            'payment_terms' => 'required|string',
            'reference_number' => 'nullable|string|max:100',
            'po_number' => 'nullable|string|max:100',
            'template' => 'required|in:modern,corporate',
            'notes' => 'nullable|string',
            'terms_conditions' => 'nullable|string',
            'status' => 'required|in:Draft,Sent,Viewed,Partially Paid,Paid,Overdue,Cancelled',
            'discount_type' => 'required|in:fixed,percentage',
            'discount_value' => 'required|numeric|min:0',
            'additional_charges' => 'nullable|numeric|min:0',
            'round_off' => 'nullable|numeric',
            'selected_taxes' => 'nullable|array',
            'items' => 'required|array|min:1',
            'items.*.item_name' => 'required|string|max:255',
            'items.*.service_id' => 'nullable|exists:services,id',
            'items.*.quantity' => 'required|numeric|min:0.01',
            'items.*.unit' => 'required|string',
            'items.*.unit_price' => 'required|numeric|min:0',
            'items.*.discount_type' => 'nullable|in:fixed,percentage',
            'items.*.discount_value' => 'nullable|numeric|min:0',
            'items.*.tax_rate' => 'nullable|numeric|min:0',
        ]);

        $updatedInvoice = $this->invoiceService->updateInvoice($invoice, $validated, $validated['items']);

        return redirect()->route('invoices.show', $updatedInvoice)->with('success', 'Invoice updated successfully.');
    }

    public function duplicate(Invoice $invoice)
    {
        $newInvoice = $this->invoiceService->duplicateInvoice($invoice);
        return redirect()->route('invoices.edit', $newInvoice)->with('success', 'Invoice duplicated as draft.');
    }

    public function cancel(Request $request, Invoice $invoice)
    {
        $this->authorize('cancel', $invoice);
        $request->validate(['cancellation_reason' => 'required|string']);

        $this->invoiceService->cancelInvoice($invoice, $request->cancellation_reason);

        return back()->with('success', 'Invoice cancelled successfully.');
    }

    public function downloadPdf(Invoice $invoice)
    {
        $pdfPath = $this->pdfService->generatePdf($invoice);
        $fileName = basename($pdfPath);
        return response()->download($pdfPath, $fileName);
    }

    public function sendEmail(Invoice $invoice)
    {
        $pdfPath = $this->pdfService->generatePdf($invoice);
        $sent = $this->emailService->sendInvoiceEmail($invoice, $pdfPath);

        if ($sent) {
            return back()->with('success', "Invoice emailed successfully to {$invoice->client->email}.");
        }
        return back()->with('error', 'Failed sending email. Please check email configuration.');
    }

    public function calculateAjax(Request $request)
    {
        $data = $request->only(['discount_type', 'discount_value', 'additional_charges', 'round_off', 'selected_taxes']);
        $items = $request->get('items', []);

        $totals = $this->invoiceService->calculateInvoiceTotals($data, $items);

        return response()->json($totals);
    }
}
