<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use App\Models\Service;
use Illuminate\Http\Request;

class ServiceController extends Controller
{
    public function index(Request $request)
    {
        $query = Service::query();

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where('service_name', 'like', "%{$search}%")
                  ->orWhere('sku', 'like', "%{$search}%");
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $services = $query->latest()->paginate(15)->withQueryString();

        return view('services.index', compact('services'));
    }

    public function create()
    {
        return view('services.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'service_name' => 'required|string|max:255',
            'sku' => 'nullable|string|unique:services,sku',
            'description' => 'nullable|string',
            'unit' => 'required|in:Hour,Day,Project,Month,License,Item',
            'default_price' => 'required|numeric|min:0',
            'tax_rate' => 'required|numeric|min:0|max:100',
            'status' => 'required|in:active,inactive',
        ]);

        $service = Service::create($validated);

        ActivityLog::log('created', "Service {$service->service_name} created", $service);

        return redirect()->route('services.index')->with('success', 'Service created successfully.');
    }

    public function edit(Service $service)
    {
        return view('services.edit', compact('service'));
    }

    public function update(Request $request, Service $service)
    {
        $validated = $request->validate([
            'service_name' => 'required|string|max:255',
            'sku' => 'nullable|string|unique:services,sku,' . $service->id,
            'description' => 'nullable|string',
            'unit' => 'required|in:Hour,Day,Project,Month,License,Item',
            'default_price' => 'required|numeric|min:0',
            'tax_rate' => 'required|numeric|min:0|max:100',
            'status' => 'required|in:active,inactive',
        ]);

        $service->update($validated);

        ActivityLog::log('updated', "Service {$service->service_name} updated", $service);

        return redirect()->route('services.index')->with('success', 'Service updated successfully.');
    }

    public function destroy(Service $service)
    {
        $name = $service->service_name;
        $service->delete();

        ActivityLog::log('deleted', "Service {$name} deleted");

        return redirect()->route('services.index')->with('success', 'Service deleted successfully.');
    }
}
