<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\BudgetLine;
use App\Models\Project;

class BudgetLineController extends Controller
{
    public function store(Request $request)
    {
        $validated = $request->validate([
            'project_id' => 'required|exists:projects,id',
            'category' => 'required|in:Material,Labor,Overhead,Equipment,Subcontractor,Other',
            'description' => 'required|string',
            'planned_amount' => 'required|numeric|min:0',
            'currency' => 'nullable|string|size:3',
            'status' => 'required|in:Planned,Approved,In Progress,Completed,Cancelled',
        ]);

        $budgetLine = BudgetLine::create($validated);

        // Update project total budget if needed
        $project = Project::find($validated['project_id']);
        $totalBudget = $project->budgetLines()->sum('planned_amount');
        $project->update(['total_budget' => $totalBudget]);
        $project->calculateVariance();

        if ($request->expectsJson()) {
            return response()->json($budgetLine->load('project'), 201);
        }

        return redirect()->route('projects.show', $project)
            ->with('success', 'Budget line added successfully.');
    }

    public function update(Request $request, BudgetLine $budgetLine)
    {
        $validated = $request->validate([
            'category' => 'required|in:Material,Labor,Overhead,Equipment,Subcontractor,Other',
            'description' => 'required|string',
            'planned_amount' => 'required|numeric|min:0',
            'actual_amount' => 'nullable|numeric|min:0',
            'currency' => 'nullable|string|size:3',
            'status' => 'required|in:Planned,Approved,In Progress,Completed,Cancelled',
        ]);

        $budgetLine->update($validated);

        // Update project total budget
        $project = $budgetLine->project;
        $totalBudget = $project->budgetLines()->sum('planned_amount');
        $actualCost = $project->budgetLines()->sum('actual_amount');
        $project->update([
            'total_budget' => $totalBudget,
            'actual_cost' => $actualCost
        ]);
        $project->calculateVariance();

        if ($request->expectsJson()) {
            return response()->json($budgetLine->load('project'));
        }

        return redirect()->route('projects.show', $project)
            ->with('success', 'Budget line updated successfully.');
    }

    public function destroy(BudgetLine $budgetLine)
    {
        $project = $budgetLine->project;
        $budgetLine->delete();

        // Update project totals
        $totalBudget = $project->budgetLines()->sum('planned_amount');
        $actualCost = $project->budgetLines()->sum('actual_amount');
        $project->update([
            'total_budget' => $totalBudget,
            'actual_cost' => $actualCost
        ]);
        $project->calculateVariance();

        if (request()->expectsJson()) {
            return response()->json(['message' => 'Budget line deleted successfully.']);
        }

        return redirect()->route('projects.show', $project)
            ->with('success', 'Budget line deleted successfully.');
    }
}