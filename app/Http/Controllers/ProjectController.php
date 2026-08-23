<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use App\Models\Client;
use App\Models\Project;
use Illuminate\Http\Request;

class ProjectController extends Controller
{
    public function index(Request $request)
    {
        $query = Project::with('client');

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('project_name', 'like', "%{$search}%")
                  ->orWhere('project_id', 'like', "%{$search}%");
            });
        }

        if ($request->filled('client_id')) {
            $query->where('client_id', $request->client_id);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $projects = $query->latest()->paginate(15)->withQueryString();
        $clients = Client::orderBy('company_name')->get();

        return view('projects.index', compact('projects', 'clients'));
    }

    public function create()
    {
        $latest = Project::orderBy('id', 'desc')->first();
        $nextNum = $latest ? ((int) str_replace('PRJ-', '', $latest->project_id)) + 1 : 1;
        $suggestedId = sprintf("PRJ-%03d", $nextNum);

        $clients = Client::where('status', 'active')->orderBy('company_name')->get();

        return view('projects.create', compact('suggestedId', 'clients'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'project_id' => 'required|unique:projects,project_id',
            'client_id' => 'required|exists:clients,id',
            'project_name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date',
            'status' => 'required|in:Planning,In Progress,On Hold,Completed,Cancelled',
            'budget' => 'required|numeric|min:0',
            'notes' => 'nullable|string',
        ]);

        $project = Project::create($validated);

        ActivityLog::log('created', "Project {$project->project_name} ({$project->project_id}) created", $project);

        return redirect()->route('projects.index')->with('success', 'Project created successfully.');
    }

    public function edit(Project $project)
    {
        $clients = Client::orderBy('company_name')->get();
        return view('projects.edit', compact('project', 'clients'));
    }

    public function update(Request $request, Project $project)
    {
        $validated = $request->validate([
            'client_id' => 'required|exists:clients,id',
            'project_name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date',
            'status' => 'required|in:Planning,In Progress,On Hold,Completed,Cancelled',
            'budget' => 'required|numeric|min:0',
            'notes' => 'nullable|string',
        ]);

        $project->update($validated);

        ActivityLog::log('updated', "Project {$project->project_name} updated", $project);

        return redirect()->route('projects.index')->with('success', 'Project updated successfully.');
    }

    public function destroy(Project $project)
    {
        $name = $project->project_name;
        $project->delete();

        ActivityLog::log('deleted', "Project {$name} deleted");

        return redirect()->route('projects.index')->with('success', 'Project deleted successfully.');
    }
}
