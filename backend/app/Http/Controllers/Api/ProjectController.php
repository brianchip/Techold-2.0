<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Project;
use App\Http\Requests\ProjectRequest;
use App\Http\Resources\ProjectResource;
use App\Http\Resources\ProjectCollection;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class ProjectController extends Controller
{
    /**
     * Display a listing of projects with filtering and pagination
     */
    public function index(Request $request): JsonResponse
    {
        try {
            $query = Project::with(['client', 'projectManager', 'tasks', 'budgetLines'])
                           ->withCount(['tasks', 'risks']);

            // Apply filters
            if ($request->filled('status')) {
                $query->byStatus($request->status);
            }

            if ($request->filled('type')) {
                $query->byType($request->type);
            }

            if ($request->filled('manager_id')) {
                $query->byManager($request->manager_id);
            }

            if ($request->filled('client_id')) {
                $query->where('client_id', $request->client_id);
            }

            if ($request->filled('overdue') && $request->boolean('overdue')) {
                $query->overdue();
            }

            if ($request->filled('search')) {
                $search = $request->search;
                $query->where(function ($q) use ($search) {
                    $q->where('project_name', 'like', "%{$search}%")
                      ->orWhere('project_code', 'like', "%{$search}%")
                      ->orWhere('description', 'like', "%{$search}%");
                });
            }

            // Apply sorting
            $sortBy = $request->get('sort_by', 'created_at');
            $sortDirection = $request->get('sort_direction', 'desc');
            $query->orderBy($sortBy, $sortDirection);

            // Pagination
            $perPage = $request->get('per_page', 15);
            $projects = $query->paginate($perPage);

            return response()->json([
                'success' => true,
                'data' => new ProjectCollection($projects),
                'message' => 'Projects retrieved successfully'
            ]);

        } catch (\Exception $e) {
            Log::error('Error retrieving projects: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error retrieving projects',
                'error' => config('app.debug') ? $e->getMessage() : null
            ], 500);
        }
    }

    /**
     * Store a newly created project
     */
    public function store(ProjectRequest $request): JsonResponse
    {
        try {
            DB::beginTransaction();

            $project = Project::create($request->validated());

            // Create project folder structure
            $project->createProjectFolderStructure();

            // If tasks are provided, create them
            if ($request->filled('tasks')) {
                foreach ($request->tasks as $taskData) {
                    $project->tasks()->create($taskData);
                }
            }

            // If budget lines are provided, create them
            if ($request->filled('budget_lines')) {
                foreach ($request->budget_lines as $budgetData) {
                    $project->budgetLines()->create($budgetData);
                }
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'data' => new ProjectResource($project->load(['client', 'projectManager', 'tasks', 'budgetLines'])),
                'message' => 'Project created successfully'
            ], 201);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error creating project: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Error creating project',
                'error' => config('app.debug') ? $e->getMessage() : null
            ], 500);
        }
    }

    /**
     * Display the specified project
     */
    public function show(Project $project): JsonResponse
    {
        try {
            $project->load([
                'client',
                'projectManager',
                'tasks' => function ($query) {
                    $query->with(['resources', 'documents']);
                },
                'budgetLines',
                'documents',
                'risks',
                'resources'
            ]);

            return response()->json([
                'success' => true,
                'data' => new ProjectResource($project),
                'message' => 'Project retrieved successfully'
            ]);

        } catch (\Exception $e) {
            Log::error('Error retrieving project: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error retrieving project',
                'error' => config('app.debug') ? $e->getMessage() : null
            ], 500);
        }
    }

    /**
     * Update the specified project
     */
    public function update(ProjectRequest $request, Project $project): JsonResponse
    {
        try {
            DB::beginTransaction();

            $project->update($request->validated());

            // Update progress if tasks changed
            if ($request->has('tasks')) {
                $project->updateProgress();
            }

            // Update actual cost if budget lines changed
            if ($request->has('budget_lines')) {
                $project->calculateActualCost();
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'data' => new ProjectResource($project->fresh(['client', 'projectManager', 'tasks', 'budgetLines'])),
                'message' => 'Project updated successfully'
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error updating project: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Error updating project',
                'error' => config('app.debug') ? $e->getMessage() : null
            ], 500);
        }
    }

    /**
     * Remove the specified project
     */
    public function destroy(Project $project): JsonResponse
    {
        try {
            DB::beginTransaction();

            // Delete project folder structure
            $projectPath = "projects/{$project->project_code} - {$project->project_name}";
            if (Storage::disk('public')->exists($projectPath)) {
                Storage::disk('public')->deleteDirectory($projectPath);
            }

            $project->delete();

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Project deleted successfully'
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error deleting project: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Error deleting project',
                'error' => config('app.debug') ? $e->getMessage() : null
            ], 500);
        }
    }

    /**
     * Get project statistics and KPIs
     */
    public function statistics(): JsonResponse
    {
        try {
            $stats = [
                'total_projects' => Project::count(),
                'active_projects' => Project::active()->count(),
                'completed_projects' => Project::byStatus('Completed')->count(),
                'overdue_projects' => Project::overdue()->count(),
                'total_budget' => Project::sum('total_budget'),
                'total_actual_cost' => Project::sum('actual_cost'),
                'projects_by_type' => Project::selectRaw('project_type, COUNT(*) as count')
                                           ->groupBy('project_type')
                                           ->get(),
                'projects_by_status' => Project::selectRaw('status, COUNT(*) as count')
                                             ->groupBy('status')
                                             ->get()
            ];

            return response()->json([
                'success' => true,
                'data' => $stats,
                'message' => 'Project statistics retrieved successfully'
            ]);

        } catch (\Exception $e) {
            Log::error('Error retrieving project statistics: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error retrieving project statistics',
                'error' => config('app.debug') ? $e->getMessage() : null
            ], 500);
        }
    }

    /**
     * Export projects to Excel/PDF
     */
    public function export(Request $request): JsonResponse
    {
        try {
            $format = $request->get('format', 'excel');
            $projects = Project::with(['client', 'projectManager'])->get();

            if ($format === 'excel') {
                // Export to Excel logic here
                return response()->json([
                    'success' => true,
                    'message' => 'Excel export functionality to be implemented',
                    'data' => $projects
                ]);
            } else {
                // Export to PDF logic here
                return response()->json([
                    'success' => true,
                    'message' => 'PDF export functionality to be implemented',
                    'data' => $projects
                ]);
            }

        } catch (\Exception $e) {
            Log::error('Error exporting projects: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error exporting projects',
                'error' => config('app.debug') ? $e->getMessage() : null
            ], 500);
        }
    }
}
