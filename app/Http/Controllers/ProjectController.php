<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Project;
use App\Models\Client;

class ProjectController extends Controller
{
    public function index()
    {
        $projects = Project::with(['client'])
            ->orderBy('created_at', 'desc')
            ->paginate(12);
            
        $clients = Client::orderBy('company_name')->get();
        
        return view('projects.index', compact('projects', 'clients'));
    }

    public function create()
    {
        $clients = Client::orderBy('company_name')->get();
        return view('projects.create', compact('clients'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'project_name' => 'required|string|max:255',
            'client_id' => 'nullable|exists:clients,id',
            'description' => 'nullable|string',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'status' => 'required|in:Planning,In Progress,On Hold,Completed,Cancelled',
            'total_budget' => 'nullable|numeric|min:0',
        ]);

        // Generate project code
        $lastProject = Project::latest()->first();
        $nextNumber = $lastProject ? (int)substr($lastProject->project_code, -4) + 1 : 1;
        $validated['project_code'] = 'PROJ-' . str_pad($nextNumber, 4, '0', STR_PAD_LEFT);

        $project = Project::create($validated);

        if ($request->expectsJson()) {
            return response()->json($project->load('client'), 201);
        }

        return redirect()->route('projects.index')
            ->with('success', 'Project created successfully.');
    }

    public function show(Project $project)
    {
        $project->load(['client', 'tasks', 'resources', 'budgetLines', 'documents', 'risks']);
        return view('projects.show', compact('project'));
    }

    public function edit(Project $project)
    {
        $clients = Client::orderBy('company_name')->get();
        return view('projects.edit', compact('project', 'clients'));
    }

    public function update(Request $request, Project $project)
    {
        $validated = $request->validate([
            'project_name' => 'required|string|max:255',
            'client_id' => 'nullable|exists:clients,id',
            'description' => 'nullable|string',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'status' => 'required|in:Planning,In Progress,On Hold,Completed,Cancelled',
            'total_budget' => 'nullable|numeric|min:0',
        ]);

        $project->update($validated);

        if ($request->expectsJson()) {
            return response()->json($project->load('client'));
        }

        return redirect()->route('projects.index')
            ->with('success', 'Project updated successfully.');
    }

    public function destroy(Project $project)
    {
        $project->delete();

        if (request()->expectsJson()) {
            return response()->json(['message' => 'Project deleted successfully.']);
        }

        return redirect()->route('projects.index')
            ->with('success', 'Project deleted successfully.');
    }
}

