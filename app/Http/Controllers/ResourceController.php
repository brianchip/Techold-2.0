<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Resource;
use App\Models\Task;
use App\Models\Project;
use App\Models\Employee;
use App\Models\Equipment;

class ResourceController extends Controller
{
    public function index(Request $request)
    {
        $query = Resource::with(['task', 'employee', 'equipment']);

        // Filter by project if specified
        if ($request->has('project_id')) {
            $query->whereHas('task', function ($q) use ($request) {
                $q->where('project_id', $request->project_id);
            });
        }

        // Filter by task if specified
        if ($request->has('task_id')) {
            $query->where('task_id', $request->task_id);
        }

        // Filter by status
        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        // Filter by resource type
        if ($request->has('type')) {
            if ($request->type === 'human') {
                $query->whereNotNull('employee_id');
            } elseif ($request->type === 'equipment') {
                $query->whereNotNull('equipment_id');
            }
        }

        $resources = $query->paginate(15)->withQueryString();

        return view('resources.index', compact('resources'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'task_id' => 'required|exists:tasks,id',
            'resource_type' => 'required|in:human,equipment',
            'employee_id' => 'nullable|exists:employees,id|required_if:resource_type,human',
            'equipment_id' => 'nullable|exists:equipment,id|required_if:resource_type,equipment',
            'role' => 'nullable|string|max:255',
            'allocated_hours' => 'required|integer|min:1',
            'hourly_rate' => 'nullable|numeric|min:0',
            'allocation_start_date' => 'required|date',
            'allocation_end_date' => 'required|date|after_or_equal:allocation_start_date',
            'notes' => 'nullable|string',
        ]);

        // Set default hourly rate if not provided
        if (!$validated['hourly_rate']) {
            if ($validated['resource_type'] === 'human' && $validated['employee_id']) {
                $employee = Employee::find($validated['employee_id']);
                $validated['hourly_rate'] = $employee->hourly_rate ?? 0;
            } elseif ($validated['resource_type'] === 'equipment' && $validated['equipment_id']) {
                $equipment = Equipment::find($validated['equipment_id']);
                $validated['hourly_rate'] = $equipment->hourly_rate ?? 0;
            }
        }

        // Calculate total cost
        $validated['total_cost'] = $validated['allocated_hours'] * ($validated['hourly_rate'] ?? 0);

        $resource = Resource::create($validated);

        if ($request->expectsJson()) {
            return response()->json($resource->load(['task', 'employee', 'equipment']), 201);
        }

        $task = Task::find($validated['task_id']);
        return redirect()->route('projects.show', $task->project_id)
            ->with('success', 'Resource allocated successfully.');
    }

    public function update(Request $request, Resource $resource)
    {
        $validated = $request->validate([
            'role' => 'nullable|string|max:255',
            'allocated_hours' => 'required|integer|min:1',
            'actual_hours' => 'nullable|integer|min:0',
            'hourly_rate' => 'nullable|numeric|min:0',
            'allocation_start_date' => 'required|date',
            'allocation_end_date' => 'required|date|after_or_equal:allocation_start_date',
            'status' => 'required|in:Allocated,Active,Completed,Cancelled',
            'notes' => 'nullable|string',
        ]);

        // Recalculate total cost
        $actualHours = $validated['actual_hours'] ?? $resource->actual_hours;
        $validated['total_cost'] = $actualHours * ($validated['hourly_rate'] ?? $resource->hourly_rate ?? 0);

        $resource->update($validated);

        if ($request->expectsJson()) {
            return response()->json($resource->load(['task', 'employee', 'equipment']));
        }

        return redirect()->route('projects.show', $resource->task->project_id)
            ->with('success', 'Resource allocation updated successfully.');
    }

    public function destroy(Resource $resource)
    {
        $projectId = $resource->task->project_id;
        $resource->delete();

        if (request()->expectsJson()) {
            return response()->json(['message' => 'Resource allocation removed successfully.']);
        }

        return redirect()->route('projects.show', $projectId)
            ->with('success', 'Resource allocation removed successfully.');
    }

    public function getAvailableResources(Request $request)
    {
        $startDate = $request->get('start_date');
        $endDate = $request->get('end_date');
        $taskId = $request->get('task_id');

        $availableEmployees = Employee::where('is_active', true)
            ->get()
            ->filter(function ($employee) use ($startDate, $endDate) {
                return $this->isEmployeeAvailable($employee, $startDate, $endDate);
            })
            ->map(function ($employee) {
                return [
                    'id' => $employee->id,
                    'name' => $employee->full_name,
                    'position' => $employee->position,
                    'hourly_rate' => $employee->hourly_rate ?? 0,
                    'type' => 'human'
                ];
            });

        $availableEquipment = Equipment::available()
            ->get()
            ->filter(function ($equipment) use ($startDate, $endDate) {
                return $equipment->isAvailableForDateRange($startDate, $endDate);
            })
            ->map(function ($equipment) {
                return [
                    'id' => $equipment->id,
                    'name' => $equipment->full_name,
                    'category' => $equipment->category,
                    'hourly_rate' => $equipment->hourly_rate ?? 0,
                    'type' => 'equipment'
                ];
            });

        return response()->json([
            'employees' => $availableEmployees->values(),
            'equipment' => $availableEquipment->values()
        ]);
    }

    public function getTaskResources(Task $task)
    {
        $resources = $task->resources()
            ->with(['employee', 'equipment'])
            ->get()
            ->map(function ($resource) {
                return [
                    'id' => $resource->id,
                    'resource_name' => $resource->resource_name,
                    'resource_type' => $resource->resource_type,
                    'role' => $resource->role,
                    'allocated_hours' => $resource->allocated_hours,
                    'actual_hours' => $resource->actual_hours,
                    'hourly_rate' => $resource->hourly_rate,
                    'total_cost' => $resource->total_cost,
                    'allocation_start_date' => $resource->allocation_start_date?->format('Y-m-d'),
                    'allocation_end_date' => $resource->allocation_end_date?->format('Y-m-d'),
                    'status' => $resource->status,
                    'utilization_rate' => $resource->getUtilizationRate(),
                    'variance' => $resource->variance,
                ];
            });

        return response()->json($resources);
    }

    public function getProjectResourceSummary(Project $project)
    {
        $resources = Resource::whereHas('task', function ($query) use ($project) {
            $query->where('project_id', $project->id);
        })->with(['task', 'employee', 'equipment'])->get();

        $summary = [
            'total_resources' => $resources->count(),
            'human_resources' => $resources->whereNotNull('employee_id')->count(),
            'equipment_resources' => $resources->whereNotNull('equipment_id')->count(),
            'total_allocated_hours' => $resources->sum('allocated_hours'),
            'total_actual_hours' => $resources->sum('actual_hours'),
            'total_planned_cost' => $resources->sum(function ($resource) {
                return $resource->allocated_hours * ($resource->hourly_rate ?? 0);
            }),
            'total_actual_cost' => $resources->sum('total_cost'),
            'resource_utilization' => $resources->count() > 0 ? 
                $resources->avg(function ($resource) {
                    return $resource->getUtilizationRate();
                }) : 0,
            'resources_by_status' => $resources->groupBy('status')->map->count(),
            'resources_by_type' => [
                'human' => $resources->whereNotNull('employee_id')->count(),
                'equipment' => $resources->whereNotNull('equipment_id')->count()
            ]
        ];

        return response()->json($summary);
    }

    private function isEmployeeAvailable($employee, $startDate, $endDate)
    {
        if (!$startDate || !$endDate) return true;

        // Check for resource allocation conflicts
        $conflict = Resource::where('employee_id', $employee->id)
            ->where(function ($query) use ($startDate, $endDate) {
                $query->whereBetween('allocation_start_date', [$startDate, $endDate])
                      ->orWhereBetween('allocation_end_date', [$startDate, $endDate])
                      ->orWhere(function ($q) use ($startDate, $endDate) {
                          $q->where('allocation_start_date', '<=', $startDate)
                            ->where('allocation_end_date', '>=', $endDate);
                      });
            })
            ->where('status', '!=', 'Cancelled')
            ->exists();

        return !$conflict;
    }
}