<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use App\Models\Client;
use App\Models\CompanySetting;
use App\Services\ClientStatementService;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class ClientController extends Controller
{
    public function index(Request $request)
    {
        $query = Client::query();

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('company_name', 'like', "%{$search}%")
                  ->orWhere('contact_person', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('client_id', 'like', "%{$search}%");
            });
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('country')) {
            $query->where('country', $request->country);
        }

        $clients = $query->latest()->paginate(15)->withQueryString();
        $countries = Client::distinct()->pluck('country')->filter()->values();

        return view('clients.index', compact('clients', 'countries'));
    }

    public function create()
    {
        // Auto generate client ID
        $latest = Client::orderBy('id', 'desc')->first();
        $nextNum = $latest ? ((int) str_replace('CLI-', '', $latest->client_id)) + 1 : 1;
        $suggestedId = sprintf("CLI-%03d", $nextNum);

        return view('clients.create', compact('suggestedId'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'client_id' => 'required|unique:clients,client_id',
            'client_type' => 'required|in:individual,company',
            'company_name' => 'required|string|max:255',
            'contact_person' => 'nullable|string|max:255',
            'email' => 'required|email|max:255',
            'secondary_email' => 'nullable|email|max:255',
            'phone' => 'nullable|string|max:50',
            'whatsapp' => 'nullable|string|max:50',
            'website' => 'nullable|url|max:255',
            'address' => 'nullable|string',
            'city' => 'nullable|string|max:100',
            'state' => 'nullable|string|max:100',
            'country' => 'nullable|string|max:100',
            'zip_code' => 'nullable|string|max:20',
            'tax_number' => 'nullable|string|max:100',
            'registration_number' => 'nullable|string|max:100',
            'currency' => 'required|string|max:10',
            'payment_terms' => 'required|string|max:50',
            'notes' => 'nullable|string',
            'status' => 'required|in:active,inactive',
        ]);

        $client = Client::create($validated);

        ActivityLog::log('created', "Client {$client->company_name} ({$client->client_id}) created", $client);

        return redirect()->route('clients.show', $client)->with('success', 'Client created successfully.');
    }

    public function show(Client $client)
    {
        $client->load(['invoices.payments', 'payments.invoice', 'projects', 'contacts']);
        
        $invoices = $client->invoices()->latest()->paginate(10, ['*'], 'invoices_page');
        $payments = $client->payments()->latest()->paginate(10, ['*'], 'payments_page');
        $projects = $client->projects()->latest()->get();
        $activities = ActivityLog::where('model_type', Client::class)
            ->where('model_id', $client->id)
            ->latest()
            ->take(10)
            ->get();

        return view('clients.show', compact('client', 'invoices', 'payments', 'projects', 'activities'));
    }

    public function edit(Client $client)
    {
        return view('clients.edit', compact('client'));
    }

    public function update(Request $request, Client $client)
    {
        $validated = $request->validate([
            'client_type' => 'required|in:individual,company',
            'company_name' => 'required|string|max:255',
            'contact_person' => 'nullable|string|max:255',
            'email' => 'required|email|max:255',
            'secondary_email' => 'nullable|email|max:255',
            'phone' => 'nullable|string|max:50',
            'whatsapp' => 'nullable|string|max:50',
            'website' => 'nullable|url|max:255',
            'address' => 'nullable|string',
            'city' => 'nullable|string|max:100',
            'state' => 'nullable|string|max:100',
            'country' => 'nullable|string|max:100',
            'zip_code' => 'nullable|string|max:20',
            'tax_number' => 'nullable|string|max:100',
            'registration_number' => 'nullable|string|max:100',
            'currency' => 'required|string|max:10',
            'payment_terms' => 'required|string|max:50',
            'notes' => 'nullable|string',
            'status' => 'required|in:active,inactive',
        ]);

        $client->update($validated);

        ActivityLog::log('updated', "Client {$client->company_name} updated", $client);

        return redirect()->route('clients.show', $client)->with('success', 'Client updated successfully.');
    }

    public function destroy(Client $client)
    {
        $this->authorize('delete', $client);

        $name = $client->company_name;
        $client->delete();

        ActivityLog::log('deleted', "Client {$name} deleted");

        return redirect()->route('clients.index')->with('success', 'Client deleted successfully.');
    }

    public function statement(Client $client, Request $request, ClientStatementService $statementService)
    {
        $startDate = $request->get('start_date', now()->startOfYear()->format('Y-m-d'));
        $endDate = $request->get('end_date', now()->format('Y-m-d'));

        $statement = $statementService->generateStatement($client, $startDate, $endDate);

        return view('clients.statement', compact('statement'));
    }

    public function downloadStatementPdf(Client $client, Request $request, ClientStatementService $statementService)
    {
        $startDate = $request->get('start_date', now()->startOfYear()->format('Y-m-d'));
        $endDate = $request->get('end_date', now()->format('Y-m-d'));

        $statement = $statementService->generateStatement($client, $startDate, $endDate);
        $company = CompanySetting::getSettings();

        $pdf = Pdf::loadView('pdf.statement', compact('statement', 'company'));
        $pdf->setPaper('A4', 'portrait');

        $slugName = Str::slug($client->company_name);
        $fileName = "Statement_{$slugName}_{$startDate}_to_{$endDate}.pdf";

        return $pdf->download($fileName);
    }
}
