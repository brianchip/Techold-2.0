<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Risk;
use App\Models\Project;

class RiskController extends Controller
{
    public function store(Request $request)
    {
        $validated = $request->validate([
            'project_id' => 'required|exists:projects,id',
            'risk_title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'severity' => 'required|in:Low,Medium,High,Critical',
            'probability' => 'required|in:Low,Medium,High',
            'impact' => 'required|in:Low,Medium,High',
            'status' => 'required|in:Identified,Analyzing,Mitigating,Resolved,Accepted',
            'mitigation_plan' => 'nullable|string',
            'owner' => 'nullable|string|max:255',
            'due_date' => 'nullable|date',
        ]);

        // Calculate risk score based on probability and impact
        $riskScore = $this->calculateRiskScore($validated['probability'], $validated['impact']);
        $validated['risk_score'] = $riskScore;

        $risk = Risk::create($validated);

        if ($request->expectsJson()) {
            return response()->json($risk->load('project'), 201);
        }

        return redirect()->route('projects.show', $validated['project_id'])
            ->with('success', 'Risk added successfully.');
    }

    public function update(Request $request, Risk $risk)
    {
        $validated = $request->validate([
            'risk_title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'severity' => 'required|in:Low,Medium,High,Critical',
            'probability' => 'required|in:Low,Medium,High',
            'impact' => 'required|in:Low,Medium,High',
            'status' => 'required|in:Identified,Analyzing,Mitigating,Resolved,Accepted',
            'mitigation_plan' => 'nullable|string',
            'owner' => 'nullable|string|max:255',
            'due_date' => 'nullable|date',
        ]);

        // Recalculate risk score
        $validated['risk_score'] = $this->calculateRiskScore($validated['probability'], $validated['impact']);

        $risk->update($validated);

        if ($request->expectsJson()) {
            return response()->json($risk->load('project'));
        }

        return redirect()->route('projects.show', $risk->project_id)
            ->with('success', 'Risk updated successfully.');
    }

    public function destroy(Risk $risk)
    {
        $projectId = $risk->project_id;
        $risk->delete();

        if (request()->expectsJson()) {
            return response()->json(['message' => 'Risk deleted successfully.']);
        }

        return redirect()->route('projects.show', $projectId)
            ->with('success', 'Risk deleted successfully.');
    }

    private function calculateRiskScore($probability, $impact)
    {
        $scoreMap = [
            'Low' => 1,
            'Medium' => 2,
            'High' => 3
        ];

        return $scoreMap[$probability] * $scoreMap[$impact];
    }
}