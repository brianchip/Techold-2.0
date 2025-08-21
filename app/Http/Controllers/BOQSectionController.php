<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\BOQSection;
use App\Models\Project;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;

class BOQSectionController extends Controller
{
    public function index(Project $project): JsonResponse
    {
        $sections = $project->boqSections()
            ->with('items')
            ->orderBy('display_order')
            ->get()
            ->map(function ($section) {
                return [
                    'id' => $section->id,
                    'section_name' => $section->section_name,
                    'section_code' => $section->section_code,
                    'description' => $section->description,
                    'total_amount' => $section->total_amount,
                    'items_count' => $section->items->count(),
                    'status' => $section->status,
                    'display_order' => $section->display_order
                ];
            });

        return response()->json([
            'success' => true,
            'sections' => $sections
        ]);
    }

    public function store(Request $request, Project $project): JsonResponse
    {
        $validated = $request->validate([
            'section_name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'display_order' => 'nullable|integer|min:0'
        ]);

        $validated['project_id'] = $project->id;
        $validated['display_order'] = $validated['display_order'] ?? 
            ($project->boqSections()->max('display_order') + 1);

        $section = BOQSection::create($validated);

        return response()->json([
            'success' => true,
            'message' => 'BOQ section created successfully',
            'section' => [
                'id' => $section->id,
                'section_name' => $section->section_name,
                'section_code' => $section->section_code,
                'description' => $section->description,
                'total_amount' => $section->total_amount,
                'items_count' => 0,
                'status' => $section->status,
                'display_order' => $section->display_order
            ]
        ]);
    }

    public function show(BOQSection $section): JsonResponse
    {
        $section->load('items', 'project');

        return response()->json([
            'success' => true,
            'section' => [
                'id' => $section->id,
                'section_name' => $section->section_name,
                'section_code' => $section->section_code,
                'description' => $section->description,
                'total_amount' => $section->total_amount,
                'items_count' => $section->items->count(),
                'status' => $section->status,
                'display_order' => $section->display_order,
                'items' => $section->items->map(function ($item) {
                    return [
                        'id' => $item->id,
                        'item_code' => $item->item_code,
                        'description' => $item->description,
                        'unit' => $item->unit,
                        'quantity' => $item->quantity,
                        'rate' => $item->rate,
                        'total_amount' => $item->total_amount,
                        'category' => $item->category,
                        'status' => $item->status
                    ];
                })
            ]
        ]);
    }

    public function update(Request $request, BOQSection $section): JsonResponse
    {
        $validated = $request->validate([
            'section_name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'status' => 'required|in:Draft,Active,Completed,Archived',
            'display_order' => 'nullable|integer|min:0'
        ]);

        $section->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'BOQ section updated successfully',
            'section' => [
                'id' => $section->id,
                'section_name' => $section->section_name,
                'section_code' => $section->section_code,
                'description' => $section->description,
                'total_amount' => $section->total_amount,
                'items_count' => $section->items()->count(),
                'status' => $section->status,
                'display_order' => $section->display_order
            ]
        ]);
    }

    public function destroy(BOQSection $section): JsonResponse
    {
        $section->delete();

        return response()->json([
            'success' => true,
            'message' => 'BOQ section deleted successfully'
        ]);
    }

    public function reorder(Request $request, Project $project): JsonResponse
    {
        $validated = $request->validate([
            'sections' => 'required|array',
            'sections.*.id' => 'required|exists:boq_sections,id',
            'sections.*.display_order' => 'required|integer|min:0'
        ]);

        foreach ($validated['sections'] as $sectionData) {
            BOQSection::where('id', $sectionData['id'])
                ->where('project_id', $project->id)
                ->update(['display_order' => $sectionData['display_order']]);
        }

        return response()->json([
            'success' => true,
            'message' => 'BOQ sections reordered successfully'
        ]);
    }

    public function export(Project $project)
    {
        $sections = $project->boqSections()
            ->with('items')
            ->orderBy('display_order')
            ->get();

        $filename = 'BOQ_' . str_replace(' ', '_', $project->project_name) . '_' . now()->format('Y-m-d') . '.csv';

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"$filename\"",
        ];

        $callback = function () use ($sections) {
            $file = fopen('php://output', 'w');
            
            // CSV Headers
            fputcsv($file, [
                'Section', 'Item Code', 'Description', 'Unit', 'Quantity', 
                'Rate', 'Total Amount', 'Category', 'Status'
            ]);

            foreach ($sections as $section) {
                foreach ($section->items as $item) {
                    fputcsv($file, [
                        $section->section_name,
                        $item->item_code,
                        $item->description,
                        $item->unit,
                        $item->quantity,
                        $item->rate,
                        $item->total_amount,
                        $item->category,
                        $item->status
                    ]);
                }
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}