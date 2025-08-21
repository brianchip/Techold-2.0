<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ProjectMilestone;
use App\Models\Project;
use Illuminate\Http\JsonResponse;

class MilestoneController extends Controller
{
    public function index(Project $project): JsonResponse
    {
        $milestones = $project->milestones()
            ->with('assignedTo')
            ->orderBy('due_date')
            ->get()
            ->map(function ($milestone) {
                return [
                    'id' => $milestone->id,
                    'milestone_name' => $milestone->milestone_name,
                    'description' => $milestone->description,
                    'due_date' => $milestone->due_date->format('Y-m-d'),
                    'completion_date' => $milestone->completion_date?->format('Y-m-d'),
                    'status' => $milestone->status,
                    'progress_percent' => $milestone->progress_percent,
                    'is_critical' => $milestone->is_critical,
                    'assigned_to' => $milestone->assignedTo?->full_name,
                    'days_until_due' => $milestone->days_until_due,
                    'is_overdue' => $milestone->is_overdue,
                    'status_color' => $milestone->status_color
                ];
            });

        return response()->json([
            'success' => true,
            'milestones' => $milestones
        ]);
    }

    public function store(Request $request, Project $project): JsonResponse
    {
        $validated = $request->validate([
            'milestone_name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'due_date' => 'required|date',
            'is_critical' => 'boolean',
            'assigned_to' => 'nullable|exists:employees,id'
        ]);

        $validated['project_id'] = $project->id;
        $validated['is_critical'] = $validated['is_critical'] ?? false;

        $milestone = ProjectMilestone::create($validated);

        return response()->json([
            'success' => true,
            'message' => 'Milestone created successfully',
            'milestone' => [
                'id' => $milestone->id,
                'milestone_name' => $milestone->milestone_name,
                'description' => $milestone->description,
                'due_date' => $milestone->due_date->format('Y-m-d'),
                'status' => $milestone->status,
                'progress_percent' => $milestone->progress_percent,
                'is_critical' => $milestone->is_critical,
                'assigned_to' => $milestone->assignedTo?->full_name,
                'days_until_due' => $milestone->days_until_due,
                'status_color' => $milestone->status_color
            ]
        ]);
    }

    public function update(Request $request, ProjectMilestone $milestone): JsonResponse
    {
        $validated = $request->validate([
            'milestone_name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'due_date' => 'required|date',
            'status' => 'required|in:Planned,In Progress,Completed,Overdue,Cancelled',
            'progress_percent' => 'required|integer|min:0|max:100',
            'is_critical' => 'boolean',
            'assigned_to' => 'nullable|exists:employees,id',
            'notes' => 'nullable|string'
        ]);

        $milestone->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Milestone updated successfully',
            'milestone' => [
                'id' => $milestone->id,
                'milestone_name' => $milestone->milestone_name,
                'description' => $milestone->description,
                'due_date' => $milestone->due_date->format('Y-m-d'),
                'completion_date' => $milestone->completion_date?->format('Y-m-d'),
                'status' => $milestone->status,
                'progress_percent' => $milestone->progress_percent,
                'is_critical' => $milestone->is_critical,
                'assigned_to' => $milestone->assignedTo?->full_name,
                'days_until_due' => $milestone->days_until_due,
                'status_color' => $milestone->status_color
            ]
        ]);
    }

    public function updateProgress(Request $request, ProjectMilestone $milestone): JsonResponse
    {
        $validated = $request->validate([
            'progress_percent' => 'required|integer|min:0|max:100'
        ]);

        $milestone->updateProgress($validated['progress_percent']);

        return response()->json([
            'success' => true,
            'message' => 'Milestone progress updated successfully',
            'milestone' => [
                'id' => $milestone->id,
                'progress_percent' => $milestone->progress_percent,
                'status' => $milestone->status,
                'completion_date' => $milestone->completion_date?->format('Y-m-d'),
                'status_color' => $milestone->status_color
            ]
        ]);
    }

    public function markCompleted(ProjectMilestone $milestone): JsonResponse
    {
        $milestone->markCompleted();

        return response()->json([
            'success' => true,
            'message' => 'Milestone marked as completed',
            'milestone' => [
                'id' => $milestone->id,
                'status' => $milestone->status,
                'progress_percent' => $milestone->progress_percent,
                'completion_date' => $milestone->completion_date->format('Y-m-d'),
                'status_color' => $milestone->status_color
            ]
        ]);
    }

    public function destroy(ProjectMilestone $milestone): JsonResponse
    {
        $milestone->delete();

        return response()->json([
            'success' => true,
            'message' => 'Milestone deleted successfully'
        ]);
    }

    public function upcoming(Project $project): JsonResponse
    {
        $upcomingMilestones = $project->milestones()
            ->upcoming()
            ->with('assignedTo')
            ->get()
            ->map(function ($milestone) {
                return [
                    'id' => $milestone->id,
                    'milestone_name' => $milestone->milestone_name,
                    'due_date' => $milestone->due_date->format('Y-m-d'),
                    'days_until_due' => $milestone->days_until_due,
                    'is_critical' => $milestone->is_critical,
                    'assigned_to' => $milestone->assignedTo?->full_name,
                    'status_color' => $milestone->status_color
                ];
            });

        return response()->json([
            'success' => true,
            'upcoming_milestones' => $upcomingMilestones
        ]);
    }

    public function overdue(Project $project): JsonResponse
    {
        $overdueMilestones = $project->milestones()
            ->overdue()
            ->with('assignedTo')
            ->get()
            ->map(function ($milestone) {
                return [
                    'id' => $milestone->id,
                    'milestone_name' => $milestone->milestone_name,
                    'due_date' => $milestone->due_date->format('Y-m-d'),
                    'days_overdue' => $milestone->days_overdue,
                    'is_critical' => $milestone->is_critical,
                    'assigned_to' => $milestone->assignedTo?->full_name
                ];
            });

        return response()->json([
            'success' => true,
            'overdue_milestones' => $overdueMilestones
        ]);
    }
}