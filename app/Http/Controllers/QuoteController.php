<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ProjectQuote;

class QuoteController extends Controller
{
    public function store(Request $request)
    {
        $validated = $request->validate([
            'project_id' => 'required|exists:projects,id',
            'supplier_name' => 'required|string|max:255',
            'supplier_contact' => 'nullable|string|max:255',
            'quote_reference' => 'nullable|string|max:255',
            'quote_amount' => 'required|numeric|min:0',
            'currency' => 'nullable|string|size:3',
            'quote_date' => 'required|date',
            'valid_until' => 'nullable|date|after_or_equal:quote_date',
            'items_description' => 'required|string',
            'is_authorized_distributor' => 'nullable|boolean',
            'is_emergency_quote' => 'nullable|boolean',
            'notes' => 'nullable|string',
        ]);

        $quote = ProjectQuote::create($validated);

        if ($request->expectsJson()) {
            return response()->json($quote->load('project'), 201);
        }

        return redirect()->route('projects.costing', $validated['project_id'])
            ->with('success', 'Quote added successfully.');
    }

    public function update(Request $request, ProjectQuote $quote)
    {
        $validated = $request->validate([
            'supplier_name' => 'required|string|max:255',
            'supplier_contact' => 'nullable|string|max:255',
            'quote_reference' => 'nullable|string|max:255',
            'quote_amount' => 'required|numeric|min:0',
            'currency' => 'nullable|string|size:3',
            'quote_date' => 'required|date',
            'valid_until' => 'nullable|date|after_or_equal:quote_date',
            'items_description' => 'required|string',
            'status' => 'required|in:Pending,Selected,Rejected,Expired',
            'is_authorized_distributor' => 'nullable|boolean',
            'is_emergency_quote' => 'nullable|boolean',
            'notes' => 'nullable|string',
        ]);

        $quote->update($validated);

        if ($request->expectsJson()) {
            return response()->json($quote->load('project'));
        }

        return redirect()->route('projects.costing', $quote->project_id)
            ->with('success', 'Quote updated successfully.');
    }

    public function destroy(ProjectQuote $quote)
    {
        $projectId = $quote->project_id;
        $quote->delete();

        if (request()->expectsJson()) {
            return response()->json(['message' => 'Quote deleted successfully.']);
        }

        return redirect()->route('projects.costing', $projectId)
            ->with('success', 'Quote deleted successfully.');
    }
}