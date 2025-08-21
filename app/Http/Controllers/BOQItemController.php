<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\BOQItem;
use App\Models\BOQSection;
use Illuminate\Http\JsonResponse;

class BOQItemController extends Controller
{
    public function store(Request $request, BOQSection $section): JsonResponse
    {
        $validated = $request->validate([
            'item_code' => 'required|string|max:50',
            'description' => 'required|string',
            'unit' => 'required|string|max:50',
            'quantity' => 'required|numeric|min:0',
            'rate' => 'required|numeric|min:0',
            'category' => 'required|in:Materials,Labor,Equipment,Subcontractor,Overhead,Other',
            'notes' => 'nullable|string'
        ]);

        $validated['boq_section_id'] = $section->id;
        $validated['project_id'] = $section->project_id;
        $validated['total_amount'] = $validated['quantity'] * $validated['rate'];

        $item = BOQItem::create($validated);

        return response()->json([
            'success' => true,
            'message' => 'BOQ item created successfully',
            'item' => [
                'id' => $item->id,
                'item_code' => $item->item_code,
                'description' => $item->description,
                'unit' => $item->unit,
                'quantity' => $item->quantity,
                'rate' => $item->rate,
                'total_amount' => $item->total_amount,
                'category' => $item->category,
                'status' => $item->status,
                'formatted_total' => $item->formatted_total
            ]
        ]);
    }

    public function update(Request $request, BOQItem $item): JsonResponse
    {
        $validated = $request->validate([
            'item_code' => 'required|string|max:50',
            'description' => 'required|string',
            'unit' => 'required|string|max:50',
            'quantity' => 'required|numeric|min:0',
            'rate' => 'required|numeric|min:0',
            'category' => 'required|in:Materials,Labor,Equipment,Subcontractor,Overhead,Other',
            'status' => 'required|in:Draft,Approved,Revised,Cancelled',
            'notes' => 'nullable|string'
        ]);

        $item->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'BOQ item updated successfully',
            'item' => [
                'id' => $item->id,
                'item_code' => $item->item_code,
                'description' => $item->description,
                'unit' => $item->unit,
                'quantity' => $item->quantity,
                'rate' => $item->rate,
                'total_amount' => $item->total_amount,
                'category' => $item->category,
                'status' => $item->status,
                'formatted_total' => $item->formatted_total
            ]
        ]);
    }

    public function destroy(BOQItem $item): JsonResponse
    {
        $item->delete();

        return response()->json([
            'success' => true,
            'message' => 'BOQ item deleted successfully'
        ]);
    }

    public function approve(BOQItem $item): JsonResponse
    {
        $item->approve();

        return response()->json([
            'success' => true,
            'message' => 'BOQ item approved successfully',
            'item' => [
                'id' => $item->id,
                'status' => $item->status,
                'total_amount' => $item->total_amount
            ]
        ]);
    }

    public function bulkUpdate(Request $request, BOQSection $section): JsonResponse
    {
        $validated = $request->validate([
            'items' => 'required|array',
            'items.*.id' => 'required|exists:boq_items,id',
            'items.*.quantity' => 'required|numeric|min:0',
            'items.*.rate' => 'required|numeric|min:0'
        ]);

        foreach ($validated['items'] as $itemData) {
            BOQItem::where('id', $itemData['id'])
                ->where('boq_section_id', $section->id)
                ->update([
                    'quantity' => $itemData['quantity'],
                    'rate' => $itemData['rate'],
                    'total_amount' => $itemData['quantity'] * $itemData['rate']
                ]);
        }

        // Update section total
        $section->updateTotalAmount();

        return response()->json([
            'success' => true,
            'message' => 'BOQ items updated successfully',
            'section_total' => $section->fresh()->total_amount
        ]);
    }
}