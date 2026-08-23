<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use App\Models\CompanySetting;
use App\Models\Currency;
use App\Models\PaymentGatewaySetting;
use App\Models\Tax;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class SettingController extends Controller
{
    public function index()
    {
        $company = CompanySetting::getSettings();
        $taxes = Tax::all();
        $currencies = Currency::all();
        $gateways = PaymentGatewaySetting::all();
        $users = User::all();

        return view('settings.index', compact('company', 'taxes', 'currencies', 'gateways', 'users'));
    }

    public function updateCompany(Request $request)
    {
        $this->authorizeAdmin();

        $validated = $request->validate([
            'company_name' => 'required|string|max:255',
            'email' => 'nullable|email|max:255',
            'phone' => 'nullable|string|max:50',
            'website' => 'nullable|url|max:255',
            'address' => 'nullable|string',
            'city' => 'nullable|string|max:100',
            'state' => 'nullable|string|max:100',
            'country' => 'nullable|string|max:100',
            'zip_code' => 'nullable|string|max:20',
            'tax_number' => 'nullable|string|max:100',
            'registration_number' => 'nullable|string|max:100',
            'bank_name' => 'nullable|string|max:255',
            'account_name' => 'nullable|string|max:255',
            'account_number' => 'nullable|string|max:100',
            'ifsc_swift' => 'nullable|string|max:100',
            'bank_address' => 'nullable|string',
            'upi_id' => 'nullable|string|max:100',
            'company_logo' => 'nullable|image|max:2048',
        ]);

        $company = CompanySetting::getSettings();

        if ($request->hasFile('company_logo')) {
            if ($company->company_logo) {
                Storage::disk('public')->delete($company->company_logo);
            }
            $path = $request->file('company_logo')->store('company', 'public');
            $validated['company_logo'] = $path;
        }

        $company->update($validated);

        ActivityLog::log('updated', 'Company profile settings updated');

        return back()->with('success', 'Company settings updated successfully.');
    }

    public function updateInvoice(Request $request)
    {
        $this->authorizeAdmin();

        $validated = $request->validate([
            'invoice_prefix' => 'required|string|max:20',
            'invoice_starting_number' => 'required|integer|min:1',
            'payment_terms' => 'required|string|max:100',
            'default_invoice_notes' => 'nullable|string',
            'default_invoice_footer' => 'nullable|string',
            'default_template' => 'required|in:modern,corporate',
        ]);

        $company = CompanySetting::getSettings();
        $company->update($validated);

        ActivityLog::log('updated', 'Invoice settings updated');

        return back()->with('success', 'Invoice settings updated successfully.');
    }

    public function storeTax(Request $request)
    {
        $this->authorizeAdmin();

        $validated = $request->validate([
            'name' => 'required|string|max:100',
            'rate' => 'required|numeric|min:0|max:100',
            'type' => 'required|string|max:50',
        ]);

        Tax::create($validated);

        return back()->with('success', 'Tax rate created successfully.');
    }

    public function deleteTax(Tax $tax)
    {
        $this->authorizeAdmin();
        $tax->delete();
        return back()->with('success', 'Tax rate deleted successfully.');
    }

    public function storeCurrency(Request $request)
    {
        $this->authorizeAdmin();

        $validated = $request->validate([
            'code' => 'required|string|max:10|unique:currencies,code',
            'name' => 'required|string|max:100',
            'symbol' => 'required|string|max:10',
            'exchange_rate' => 'required|numeric|min:0.000001',
        ]);

        Currency::create($validated);

        return back()->with('success', 'Currency added successfully.');
    }

    public function updateGateways(Request $request)
    {
        $this->authorizeAdmin();

        $gateways = $request->input('gateways', []);

        foreach ($gateways as $name => $data) {
            PaymentGatewaySetting::updateOrCreate(
                ['gateway_name' => ucfirst($name)],
                [
                    'api_key' => $data['api_key'] ?? null,
                    'secret_key' => $data['secret_key'] ?? null,
                    'webhook_secret' => $data['webhook_secret'] ?? null,
                    'environment' => $data['environment'] ?? 'test',
                    'is_active' => isset($data['is_active']) && $data['is_active'] == '1',
                ]
            );
        }

        ActivityLog::log('updated', 'Payment gateway credentials updated');

        return back()->with('success', 'Payment gateway settings updated successfully.');
    }

    public function storeUser(Request $request)
    {
        $this->authorizeAdmin();

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:8',
            'role' => 'required|in:admin,staff',
            'phone' => 'nullable|string|max:50',
        ]);

        $validated['password'] = Hash::make($validated['password']);

        User::create($validated);

        return back()->with('success', 'User account created successfully.');
    }

    protected function authorizeAdmin()
    {
        if (!auth()->user()->isAdmin()) {
            abort(403, 'Admin privilege required.');
        }
    }
}
