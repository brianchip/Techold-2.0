<?php

namespace App\Http\Controllers;

use App\Models\BOQVersion;
use App\Models\Project;
use App\Models\Employee;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\JsonResponse;

class BOQVersionController extends Controller
{
    public function index(Project $project): JsonResponse
    {
        $versions = $project->boqVersions()
            ->with(['creator', 'approver'])
            ->orderBy('created_at', 'desc')
            ->get();
            
        return response()->json([
            'success' => true,
            'versions' => $versions
        ]);
    }

    public function store(Request $request, Project $project): JsonResponse
    {
        $request->validate([
            'version_name' => 'nullable|string|max:255',
            'description' => 'nullable|string',
        ]);

        try {
            DB::beginTransaction();

            // Generate next version number
            $lastVersion = $project->boqVersions()
                ->orderBy('created_at', 'desc')
                ->first();
                
            $nextVersionNumber = $lastVersion 
                ? $this->incrementVersion($lastVersion->version_number)
                : '1.0';

            // Get first employee as creator (fallback)
            $createdBy = Employee::first()?->id ?? 1;

            // Create new version
            $version = $project->boqVersions()->create([
                'version_number' => $nextVersionNumber,
                'version_name' => $request->version_name,
                'description' => $request->description ?? "Version created on " . now()->format('M j, Y'),
                'status' => 'Draft',
                'is_current' => false,
                'created_by' => $createdBy,
            ]);

            // Create snapshot of current BOQ state
            $version->createSnapshot();

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'BOQ version created successfully!',
                'version' => $version->load(['creator', 'approver'])
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Failed to create BOQ version: ' . $e->getMessage()
            ], 500);
        }
    }

    public function show(Project $project, BOQVersion $version): JsonResponse
    {
        $version->load(['creator', 'approver', 'approvals.approver']);
        
        return response()->json([
            'success' => true,
            'version' => $version
        ]);
    }

    public function update(Request $request, Project $project, BOQVersion $version): JsonResponse
    {
        $request->validate([
            'version_name' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'status' => 'nullable|in:Draft,Active,Archived,Approved',
        ]);

        try {
            DB::beginTransaction();

            $version->update($request->only([
                'version_name', 
                'description', 
                'status'
            ]));

            // Update snapshot if needed
            if ($request->has('update_snapshot') && $request->update_snapshot) {
                $version->createSnapshot();
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'BOQ version updated successfully!',
                'version' => $version->fresh(['creator', 'approver'])
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Failed to update BOQ version: ' . $e->getMessage()
            ], 500);
        }
    }

    public function destroy(Project $project, BOQVersion $version): JsonResponse
    {
        try {
            DB::beginTransaction();

            // Don't allow deletion of current version
            if ($version->is_current) {
                return response()->json([
                    'success' => false,
                    'message' => 'Cannot delete the current active version'
                ], 400);
            }

            $version->delete();

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'BOQ version deleted successfully!'
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete BOQ version: ' . $e->getMessage()
            ], 500);
        }
    }

    public function makeCurrent(Project $project, BOQVersion $version): JsonResponse
    {
        try {
            DB::beginTransaction();

            $version->makeCurrent();

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'BOQ version set as current successfully!',
                'version' => $version->fresh()
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Failed to set BOQ version as current: ' . $e->getMessage()
            ], 500);
        }
    }

    public function restore(Project $project, BOQVersion $version): JsonResponse
    {
        try {
            DB::beginTransaction();

            $restored = $version->restore();

            if (!$restored) {
                return response()->json([
                    'success' => false,
                    'message' => 'Cannot restore version - no snapshot data available'
                ], 400);
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'BOQ restored to version ' . $version->version_number . ' successfully!'
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Failed to restore BOQ version: ' . $e->getMessage()
            ], 500);
        }
    }

    public function compare(Project $project, BOQVersion $version1, BOQVersion $version2): JsonResponse
    {
        try {
            $comparison = [
                'version1' => [
                    'version' => $version1->version_number,
                    'total_amount' => $version1->total_amount,
                    'sections_count' => $version1->sections_count,
                    'items_count' => $version1->items_count,
                    'data' => $version1->snapshot_data,
                ],
                'version2' => [
                    'version' => $version2->version_number,
                    'total_amount' => $version2->total_amount,
                    'sections_count' => $version2->sections_count,
                    'items_count' => $version2->items_count,
                    'data' => $version2->snapshot_data,
                ],
                'differences' => [
                    'amount_diff' => $version2->total_amount - $version1->total_amount,
                    'sections_diff' => $version2->sections_count - $version1->sections_count,
                    'items_diff' => $version2->items_count - $version1->items_count,
                ]
            ];

            return response()->json([
                'success' => true,
                'comparison' => $comparison
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to compare versions: ' . $e->getMessage()
            ], 500);
        }
    }

    public function export(Project $project, BOQVersion $version): \Symfony\Component\HttpFoundation\StreamedResponse
    {
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="boq_version_' . $version->version_number . '_' . $project->project_code . '.csv"',
        ];

        $callback = function() use ($version) {
            $file = fopen('php://output', 'w');
            
            // Add version header
            fputcsv($file, ['BOQ Version Export']);
            fputcsv($file, ['Version', $version->version_number]);
            fputcsv($file, ['Version Name', $version->version_name]);
            fputcsv($file, ['Total Amount', '$' . number_format($version->total_amount, 2)]);
            fputcsv($file, ['Created', $version->created_at->format('M j, Y g:i A')]);
            fputcsv($file, []);
            
            // Add data headers
            fputcsv($file, ['Section Name', 'Section Code', 'Item Code', 'Description', 'Unit', 'Quantity', 'Rate', 'Total Amount', 'Status']);

            if ($version->snapshot_data && isset($version->snapshot_data['sections'])) {
                foreach ($version->snapshot_data['sections'] as $section) {
                    if (isset($section['boq_items'])) {
                        foreach ($section['boq_items'] as $item) {
                            fputcsv($file, [
                                $section['section_name'],
                                $section['section_code'],
                                $item['item_code'],
                                $item['description'],
                                $item['unit'],
                                $item['quantity'],
                                '$' . number_format($item['rate'], 2),
                                '$' . number_format($item['total_amount'], 2),
                                $item['status'],
                            ]);
                        }
                    }
                }
            }
            
            fclose($file);
        };

        return new \Symfony\Component\HttpFoundation\StreamedResponse($callback, 200, $headers);
    }

    public function getNextSequence(Project $project): JsonResponse
    {
        try {
            // Get the highest sequence number for this project
            $latestVersion = $project->boqVersions()
                ->orderBy('created_at', 'desc')
                ->first();
            
            $nextSequence = 1;
            
            if ($latestVersion) {
                // Extract sequence from version number (assuming format like "1.0", "1.1", etc.)
                $versionParts = explode('.', $latestVersion->version_number);
                $currentSequence = (int)($versionParts[0] ?? 0);
                $nextSequence = $currentSequence + 1;
            }
            
            // Also check for any existing BOQ reference patterns
            $existingBOQs = $project->boqVersions()
                ->where('version_number', 'like', '%BOQ-%')
                ->count();
            
            if ($existingBOQs > 0) {
                $nextSequence = $existingBOQs + 1;
            }
            
            return response()->json([
                'success' => true,
                'next_sequence' => $nextSequence,
                'suggested_format' => "BOQ-{$project->project_code}-" . str_pad($nextSequence, 3, '0', STR_PAD_LEFT)
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to get next sequence: ' . $e->getMessage(),
                'next_sequence' => 1 // Fallback
            ], 500);
        }
    }

    private function incrementVersion(string $currentVersion): string
    {
        $parts = explode('.', $currentVersion);
        $major = (int)($parts[0] ?? 1);
        $minor = (int)($parts[1] ?? 0);
        
        // Increment minor version
        $minor++;
        
        return "{$major}.{$minor}";
    }
}