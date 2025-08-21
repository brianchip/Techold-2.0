<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Project;
use App\Models\Client;
use App\Models\Employee;
use Barryvdh\DomPDF\Facade\Pdf;

class ProjectController extends Controller
{
    public function index(Request $request)
    {
        $query = Project::with(['client', 'projectManager'])
            ->select('projects.*');

        // Apply filters
        if ($request->filled('status') && $request->status !== 'All Status') {
            $query->where('status', $request->status);
        }

        if ($request->filled('client_id') && $request->client_id !== 'All Clients') {
            $query->where('client_id', $request->client_id);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('project_name', 'LIKE', "%{$search}%")
                  ->orWhere('project_code', 'LIKE', "%{$search}%")
                  ->orWhere('description', 'LIKE', "%{$search}%");
            });
        }

        // Apply sorting
        $sortBy = $request->get('sort_by', 'created_at');
        $sortOrder = $request->get('sort_order', 'desc');
        
        $allowedSorts = ['created_at', 'project_name', 'status', 'progress_percent', 'end_date'];
        if (in_array($sortBy, $allowedSorts)) {
            $query->orderBy($sortBy, $sortOrder);
        } else {
            $query->orderBy('created_at', 'desc');
        }

        $projects = $query->paginate(12)->withQueryString();
            
        // Cache these for 5 minutes since they don't change often
        $clients = cache()->remember('clients_list', 300, function() {
            return Client::orderBy('company_name')->get();
        });
        
        $employees = cache()->remember('employees_list', 300, function() {
            return Employee::where('is_active', true)->orderBy('first_name')->get();
        });
        
        return view('projects.index', compact('projects', 'clients', 'employees'));
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
        $project->load([
            'client',
            'projectManager',
            'tasks',
            'resources',
            'budgetLines',
            'documents',
            'risks'
        ]);
        return view('projects.show', compact('project'));
    }

    /**
     * Show the costing dashboard for a project
     */
    public function costing(Project $project)
    {
        $project->load([
            'client',
            'projectManager',
            'primeMover',
            'engineeringManager',
            'financeManager',
            'managingDirector',
            'quotes' => function($query) {
                $query->orderBy('quote_date', 'desc');
            },
            'approvals' => function($query) {
                $query->with('approver')->orderBy('created_at', 'desc');
            }
        ]);

        // Recalculate variance if needed
        $project->calculateVariance();

        return view('projects.costing', compact('project'));
    }

    public function edit(Project $project)
    {
        $clients = Client::orderBy('company_name')->get();
        $employees = Employee::where('is_active', true)->orderBy('first_name')->get();
        return view('projects.edit', compact('project', 'clients', 'employees'));
    }

    public function update(ProjectRequest $request, Project $project)
    {
        $project->update($request->validated());

        // Recalculate variance after update
        $project->calculateVariance();

        return redirect()->route('projects.show', $project)
            ->with('success', 'Project updated successfully.');
    }

    /**
     * Export project as PDF report
     */
    public function exportPdf(Project $project)
    {
        $project->load([
            'client',
            'projectManager',
            'primeMover',
            'tasks',
            'budgetLines',
            'documents',
            'risks'
        ]);

        $pdf = Pdf::loadView('projects.pdf-report', compact('project'));
        
        return $pdf->download($project->project_code . '_Project_Report.pdf');
    }

    /**
     * Share project (generate shareable link)
     */
    public function share(Project $project)
    {
        // Generate a shareable token (in a real app, you'd store this in DB)
        $shareToken = base64_encode($project->id . ':' . time());
        $shareUrl = url("/projects/shared/{$shareToken}");

        if (request()->expectsJson()) {
            return response()->json([
                'share_url' => $shareUrl,
                'message' => 'Share link generated successfully'
            ]);
        }

        return back()->with('success', "Share link: {$shareUrl}");
    }

    /**
     * Submit project for approval
     */
    public function submitApproval(Request $request, Project $project)
    {
        $validated = $request->validate([
            'approval_type' => 'required|string',
            'approver_role' => 'required|string',
            'comments' => 'nullable|string',
        ]);

        // Find the appropriate approver (in a real system, you'd have a proper user management system)
        $approverId = 1; // Placeholder - would be determined based on approver_role

        $approval = $project->submitForApproval(
            $validated['approval_type'],
            $approverId,
            $validated['approver_role'],
            ['comments' => $validated['comments']]
        );

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Project submitted for approval successfully',
                'approval' => $approval
            ]);
        }

        return back()->with('success', 'Project submitted for approval successfully.');
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

