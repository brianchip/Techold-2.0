<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\BOQSection;
use App\Models\BOQItem;
use App\Models\BOQVersion;
use App\Models\Project;
use App\Models\Employee;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\JsonResponse;
use Barryvdh\DomPDF\Facade\Pdf;

class BOQController extends Controller
{
    public function saveDraft(Request $request): JsonResponse
    {
        $request->validate([
            'project_id' => 'required|exists:projects,id',
            'boq_reference' => 'required|string|max:255',
            'boq_version' => 'nullable|string|max:50',
            'prepared_by' => 'required|exists:employees,id',
            'sections' => 'required|array|min:1',
            'sections.*.name' => 'required|string|max:255',
            'sections.*.items' => 'required|array|min:1',
        ]);

        try {
            DB::beginTransaction();

            $project = Project::findOrFail($request->project_id);
            
            // Create BOQ version for draft
            $version = BOQVersion::create([
                'project_id' => $project->id,
                'version_number' => $request->boq_version ?: '1.0',
                'version_name' => 'Draft - ' . now()->format('M j, Y g:i A'),
                'description' => 'BOQ Draft saved from creation form',
                'status' => 'Draft',
                'is_current' => false,
                'created_by' => $request->prepared_by,
                'snapshot_data' => $request->all(),
            ]);

            // Process sections and items
            $totalAmount = 0;
            $sectionsCount = 0;
            $itemsCount = 0;

            foreach ($request->sections as $sectionData) {
                // Handle section code - ensure uniqueness
                $sectionCode = $sectionData['code'] ?? $this->generateSectionCode($sectionData['name'], $sectionsCount + 1, $project->id);
                
                // If a custom code was provided, ensure it's unique
                if (!empty($sectionData['code'])) {
                    $originalCode = $sectionCode;
                    $counter = 1;
                    while (BOQSection::where('section_code', $sectionCode)->exists()) {
                        $sectionCode = $originalCode . '-' . $counter;
                        $counter++;
                    }
                }
                
                $section = BOQSection::create([
                    'project_id' => $project->id,
                    'section_name' => $sectionData['name'],
                    'section_code' => $sectionCode,
                    'description' => $sectionData['description'] ?? '',
                    'status' => 'Draft',
                    'display_order' => $sectionsCount + 1,
                ]);

                $sectionTotal = 0;
                foreach ($sectionData['items'] as $itemData) {
                    $itemTotal = ($itemData['quantity'] ?? 0) * ($itemData['rate'] ?? 0);
                    
                    BOQItem::create([
                        'boq_section_id' => $section->id,
                        'project_id' => $project->id,
                        'item_code' => $itemData['code'],
                        'description' => $itemData['description'],
                        'unit' => $itemData['unit'],
                        'quantity' => $itemData['quantity'] ?? 0,
                        'rate' => $itemData['rate'] ?? 0,
                        'total_amount' => $itemTotal,
                        'status' => 'Draft',
                        'notes' => $itemData['notes'] ?? '',
                    ]);

                    $sectionTotal += $itemTotal;
                    $itemsCount++;
                }

                $totalAmount += $sectionTotal;
                $sectionsCount++;
            }

            // Update version with calculated totals
            $version->update([
                'total_amount' => $totalAmount,
                'sections_count' => $sectionsCount,
                'items_count' => $itemsCount,
            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'BOQ saved as draft successfully!',
                'version_id' => $version->id,
                'total_amount' => $totalAmount,
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Failed to save BOQ draft: ' . $e->getMessage()
            ], 500);
        }
    }

    public function create(Request $request): JsonResponse
    {
        $request->validate([
            'project_id' => 'required|exists:projects,id',
            'boq_reference' => 'required|string|max:255|unique:boq_versions,version_number',
            'boq_version' => 'nullable|string|max:50',
            'prepared_by' => 'required|exists:employees,id',
            'sections' => 'required|array|min:1',
            'sections.*.name' => 'required|string|max:255',
            'sections.*.items' => 'required|array|min:1',
            'sections.*.items.*.code' => 'required|string|max:255',
            'sections.*.items.*.description' => 'required|string',
            'sections.*.items.*.unit' => 'required|string|max:50',
            'sections.*.items.*.quantity' => 'required|numeric|min:0.01',
            'sections.*.items.*.rate' => 'required|numeric|min:0.01',
        ]);

        try {
            DB::beginTransaction();

            $project = Project::findOrFail($request->project_id);
            
            // Create BOQ version
            $version = BOQVersion::create([
                'project_id' => $project->id,
                'version_number' => $request->boq_reference,
                'version_name' => $request->boq_version ?: 'Version 1.0',
                'description' => 'BOQ created from form submission',
                'status' => 'Active',
                'is_current' => true,
                'created_by' => $request->prepared_by,
                'snapshot_data' => $request->all(),
            ]);

            // Make this the current version
            $version->makeCurrent();

            // Process sections and items
            $totalAmount = 0;
            $sectionsCount = 0;
            $itemsCount = 0;

            foreach ($request->sections as $sectionData) {
                // Handle section code - ensure uniqueness
                $sectionCode = $sectionData['code'] ?? $this->generateSectionCode($sectionData['name'], $sectionsCount + 1, $project->id);
                
                // If a custom code was provided, ensure it's unique
                if (!empty($sectionData['code'])) {
                    $originalCode = $sectionCode;
                    $counter = 1;
                    while (BOQSection::where('section_code', $sectionCode)->exists()) {
                        $sectionCode = $originalCode . '-' . $counter;
                        $counter++;
                    }
                }
                
                $section = BOQSection::create([
                    'project_id' => $project->id,
                    'section_name' => $sectionData['name'],
                    'section_code' => $sectionCode,
                    'description' => $sectionData['description'] ?? '',
                    'status' => 'Active',
                    'display_order' => $sectionsCount + 1,
                ]);

                $sectionTotal = 0;
                foreach ($sectionData['items'] as $itemData) {
                    $itemTotal = $itemData['quantity'] * $itemData['rate'];
                    
                    BOQItem::create([
                        'boq_section_id' => $section->id,
                        'project_id' => $project->id,
                        'item_code' => $itemData['code'],
                        'description' => $itemData['description'],
                        'unit' => $itemData['unit'],
                        'quantity' => $itemData['quantity'],
                        'rate' => $itemData['rate'],
                        'total_amount' => $itemTotal,
                        'status' => 'Approved',
                        'notes' => $itemData['notes'] ?? '',
                    ]);

                    $sectionTotal += $itemTotal;
                    $itemsCount++;
                }

                $totalAmount += $sectionTotal;
                $sectionsCount++;
            }

            // Update version with calculated totals
            $version->update([
                'total_amount' => $totalAmount,
                'sections_count' => $sectionsCount,
                'items_count' => $itemsCount,
            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'BOQ created successfully!',
                'boq_id' => $version->id,
                'version_id' => $version->id,
                'total_amount' => $totalAmount,
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Failed to create BOQ: ' . $e->getMessage()
            ], 500);
        }
    }

    public function preview(Request $request)
    {
        try {
            $project = Project::findOrFail($request->project_id);
            $preparedBy = Employee::find($request->prepared_by);

            // Calculate totals
            $totalAmount = 0;
            $sectionsCount = count($request->sections);
            $itemsCount = 0;

            foreach ($request->sections as $section) {
                foreach ($section['items'] as $item) {
                    $totalAmount += ($item['quantity'] ?? 0) * ($item['rate'] ?? 0);
                    $itemsCount++;
                }
            }

            // Prepare data for PDF
            $data = [
                'project' => $project,
                'boq_reference' => $request->boq_reference,
                'boq_version' => $request->boq_version ?: '1.0',
                'preparation_date' => $request->preparation_date,
                'prepared_by' => $preparedBy,
                'currency' => $request->currency ?: 'USD',
                'valid_until' => $request->valid_until,
                'payment_terms' => $request->payment_terms,
                'delivery_timeline' => $request->delivery_timeline,
                'warranty_period' => $request->warranty_period,
                'special_conditions' => $request->special_conditions,
                'include_taxes' => $request->include_taxes,
                'subject_to_approval' => $request->subject_to_approval,
                'sections' => $request->sections,
                'total_amount' => $totalAmount,
                'sections_count' => $sectionsCount,
                'items_count' => $itemsCount,
                'generated_at' => now(),
            ];

            // Generate PDF
            $pdf = Pdf::loadView('boq.preview', $data);
            $pdf->setPaper('A4', 'portrait');
            
            return $pdf->stream("BOQ_Preview_{$request->boq_reference}.pdf");

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to generate BOQ preview: ' . $e->getMessage()
            ], 500);
        }
    }

    private function generateSectionCode(string $sectionName, int $order, int $projectId): string
    {
        // Generate a section code from the section name
        $words = explode(' ', $sectionName);
        $code = '';
        
        foreach ($words as $word) {
            if (strlen($word) > 0) {
                $code .= strtoupper(substr($word, 0, 3));
            }
            if (strlen($code) >= 6) break;
        }
        
        // Use microtime for better uniqueness
        $microtime = microtime(true);
        $uniqueSuffix = substr(str_replace('.', '', $microtime), -6);
        
        $baseCode = $code . '-' . str_pad($order, 3, '0', STR_PAD_LEFT) . '-' . $uniqueSuffix;
        
        return $baseCode;
    }
}