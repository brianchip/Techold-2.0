<?php

namespace App\Http\Controllers;

use App\Models\BOQLibraryItem;
use App\Models\BOQSection;
use App\Models\Employee;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\JsonResponse;

class BOQLibraryController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $query = BOQLibraryItem::query()->with(['creator', 'updater']);

        // Apply filters
        if ($request->has('category') && $request->category !== 'all') {
            $query->byCategory($request->category);
        }

        if ($request->has('search') && $request->search) {
            $query->search($request->search);
        }

        if ($request->has('active_only') && $request->active_only) {
            $query->active();
        }

        if ($request->has('templates_only') && $request->templates_only) {
            $query->templates();
        }

        // Apply sorting
        $sortBy = $request->get('sort_by', 'updated_at');
        $sortOrder = $request->get('sort_order', 'desc');
        
        if ($sortBy === 'popular') {
            $query->popular();
        } else {
            $query->orderBy($sortBy, $sortOrder);
        }

        $perPage = $request->get('per_page', 20);
        $items = $query->paginate($perPage);

        return response()->json([
            'success' => true,
            'items' => $items
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'item_code' => 'required|string|max:255|unique:boq_library_items,item_code',
            'item_name' => 'required|string|max:255',
            'description' => 'required|string',
            'category' => 'required|in:Materials,Labor,Equipment,Subcontractor,Overhead,Other',
            'unit' => 'required|string|max:50',
            'standard_rate' => 'required|numeric|min:0',
            'min_rate' => 'nullable|numeric|min:0|lt:standard_rate',
            'max_rate' => 'nullable|numeric|min:0|gt:standard_rate',
            'supplier' => 'nullable|string|max:255',
            'specifications' => 'nullable|string',
            'custom_fields' => 'nullable|array',
            'is_template' => 'boolean',
        ]);

        try {
            DB::beginTransaction();

            // Get creator (fallback to first employee)
            $createdBy = Employee::first()?->id ?? 1;

            $item = BOQLibraryItem::create(array_merge(
                $request->only([
                    'item_code', 'item_name', 'description', 'category', 'unit',
                    'standard_rate', 'min_rate', 'max_rate', 'supplier', 
                    'specifications', 'custom_fields', 'is_template'
                ]),
                [
                    'is_active' => true,
                    'last_updated_price' => now(),
                    'created_by' => $createdBy,
                ]
            ));

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Library item created successfully!',
                'item' => $item->load(['creator', 'updater'])
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Failed to create library item: ' . $e->getMessage()
            ], 500);
        }
    }

    public function show(BOQLibraryItem $item): JsonResponse
    {
        $item->load(['creator', 'updater']);
        
        return response()->json([
            'success' => true,
            'item' => $item
        ]);
    }

    public function update(Request $request, BOQLibraryItem $item): JsonResponse
    {
        $request->validate([
            'item_code' => 'required|string|max:255|unique:boq_library_items,item_code,' . $item->id,
            'item_name' => 'required|string|max:255',
            'description' => 'required|string',
            'category' => 'required|in:Materials,Labor,Equipment,Subcontractor,Overhead,Other',
            'unit' => 'required|string|max:50',
            'standard_rate' => 'required|numeric|min:0',
            'min_rate' => 'nullable|numeric|min:0|lt:standard_rate',
            'max_rate' => 'nullable|numeric|min:0|gt:standard_rate',
            'supplier' => 'nullable|string|max:255',
            'specifications' => 'nullable|string',
            'custom_fields' => 'nullable|array',
            'is_active' => 'boolean',
            'is_template' => 'boolean',
        ]);

        try {
            DB::beginTransaction();

            // Get updater (fallback to first employee)
            $updatedBy = Employee::first()?->id ?? 1;

            $item->update(array_merge(
                $request->only([
                    'item_code', 'item_name', 'description', 'category', 'unit',
                    'standard_rate', 'min_rate', 'max_rate', 'supplier', 
                    'specifications', 'custom_fields', 'is_active', 'is_template'
                ]),
                [
                    'last_updated_price' => now(),
                    'updated_by' => $updatedBy,
                ]
            ));

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Library item updated successfully!',
                'item' => $item->fresh(['creator', 'updater'])
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Failed to update library item: ' . $e->getMessage()
            ], 500);
        }
    }

    public function destroy(BOQLibraryItem $item): JsonResponse
    {
        try {
            DB::beginTransaction();

            $item->delete();

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Library item deleted successfully!'
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete library item: ' . $e->getMessage()
            ], 500);
        }
    }

    public function addToBOQ(Request $request, BOQLibraryItem $item): JsonResponse
    {
        $request->validate([
            'boq_section_id' => 'required|exists:boq_sections,id',
            'quantity' => 'required|numeric|min:0',
            'override_rate' => 'nullable|numeric|min:0',
            'notes' => 'nullable|string',
        ]);

        try {
            DB::beginTransaction();

            $overrides = [];
            if ($request->has('override_rate')) {
                $overrides['rate'] = $request->override_rate;
                $overrides['total_amount'] = $request->quantity * $request->override_rate;
            }

            if ($request->has('notes')) {
                $overrides['notes'] = $request->notes;
            }

            $boqItem = $item->createBOQItem(
                $request->boq_section_id,
                $request->quantity,
                $overrides
            );

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Item added to BOQ successfully!',
                'boq_item' => $boqItem
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Failed to add item to BOQ: ' . $e->getMessage()
            ], 500);
        }
    }

    public function duplicate(Request $request, BOQLibraryItem $item): JsonResponse
    {
        $request->validate([
            'new_item_code' => 'required|string|max:255|unique:boq_library_items,item_code',
        ]);

        try {
            DB::beginTransaction();

            // Get creator (fallback to first employee)
            $createdBy = Employee::first()?->id ?? 1;

            $duplicatedItem = $item->duplicate(
                $request->new_item_code,
                $createdBy
            );

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Library item duplicated successfully!',
                'item' => $duplicatedItem->load(['creator', 'updater'])
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Failed to duplicate library item: ' . $e->getMessage()
            ], 500);
        }
    }

    public function updatePrice(Request $request, BOQLibraryItem $item): JsonResponse
    {
        $request->validate([
            'new_rate' => 'required|numeric|min:0',
        ]);

        try {
            DB::beginTransaction();

            // Get updater (fallback to first employee)
            $updatedBy = Employee::first()?->id ?? 1;

            $item->updatePrice($request->new_rate, $updatedBy);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Item price updated successfully!',
                'item' => $item->fresh()
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Failed to update item price: ' . $e->getMessage()
            ], 500);
        }
    }

    public function categories(): JsonResponse
    {
        $categories = BOQLibraryItem::getCategoryOptions();
        
        return response()->json([
            'success' => true,
            'categories' => $categories
        ]);
    }

    public function popular(Request $request): JsonResponse
    {
        $limit = $request->get('limit', 10);
        
        $items = BOQLibraryItem::active()
            ->popular()
            ->take($limit)
            ->get();

        return response()->json([
            'success' => true,
            'popular_items' => $items
        ]);
    }

    public function templates(Request $request): JsonResponse
    {
        $items = BOQLibraryItem::active()
            ->templates()
            ->with(['creator', 'updater'])
            ->get();

        return response()->json([
            'success' => true,
            'templates' => $items
        ]);
    }

    public function pricesNeedUpdate(): JsonResponse
    {
        $items = BOQLibraryItem::active()
            ->get()
            ->filter(fn($item) => $item->price_needs_update)
            ->values();

        return response()->json([
            'success' => true,
            'items_needing_update' => $items
        ]);
    }

    public function export(Request $request): \Symfony\Component\HttpFoundation\StreamedResponse
    {
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="boq_library_export_' . now()->format('Y-m-d') . '.csv"',
        ];

        $callback = function() use ($request) {
            $file = fopen('php://output', 'w');
            
            // Add headers
            fputcsv($file, [
                'Item Code', 'Item Name', 'Description', 'Category', 'Unit', 
                'Standard Rate', 'Min Rate', 'Max Rate', 'Supplier', 
                'Specifications', 'Usage Count', 'Is Active', 'Is Template'
            ]);

            // Get items with filters
            $query = BOQLibraryItem::query();
            
            if ($request->has('category') && $request->category !== 'all') {
                $query->byCategory($request->category);
            }

            $query->chunk(100, function ($items) use ($file) {
                foreach ($items as $item) {
                    fputcsv($file, [
                        $item->item_code,
                        $item->item_name,
                        $item->description,
                        $item->category,
                        $item->unit,
                        $item->standard_rate,
                        $item->min_rate,
                        $item->max_rate,
                        $item->supplier,
                        $item->specifications,
                        $item->usage_count,
                        $item->is_active ? 'Yes' : 'No',
                        $item->is_template ? 'Yes' : 'No',
                    ]);
                }
            });
            
            fclose($file);
        };

        return new \Symfony\Component\HttpFoundation\StreamedResponse($callback, 200, $headers);
    }
}