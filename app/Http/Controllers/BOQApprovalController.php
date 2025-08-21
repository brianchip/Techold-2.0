<?php

namespace App\Http\Controllers;

use App\Models\BOQApproval;
use App\Models\BOQVersion;
use App\Models\Project;
use App\Models\Employee;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\JsonResponse;

class BOQApprovalController extends Controller
{
    public function index(Project $project): JsonResponse
    {
        $approvals = BOQApproval::where('project_id', $project->id)
            ->with(['boqVersion', 'approver'])
            ->orderBy('approval_order')
            ->orderBy('created_at', 'desc')
            ->get();
            
        return response()->json([
            'success' => true,
            'approvals' => $approvals
        ]);
    }

    public function submit(Request $request, Project $project): JsonResponse
    {
        $request->validate([
            'boq_version_id' => 'required|exists:boq_versions,id',
            'comments' => 'nullable|string',
        ]);

        try {
            DB::beginTransaction();

            $boqVersion = BOQVersion::findOrFail($request->boq_version_id);
            
            // Check if already submitted for approval
            $existingApprovals = BOQApproval::where('project_id', $project->id)
                ->where('boq_version_id', $boqVersion->id)
                ->exists();
                
            if ($existingApprovals) {
                return response()->json([
                    'success' => false,
                    'message' => 'BOQ version already submitted for approval'
                ], 400);
            }

            // Create standard approval workflow
            BOQApproval::createStandardWorkflow(
                $project->id, 
                $boqVersion->id, 
                $boqVersion->total_amount
            );

            // Update BOQ version status
            $boqVersion->update(['status' => 'Under Review']);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'BOQ submitted for approval successfully!'
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Failed to submit BOQ for approval: ' . $e->getMessage()
            ], 500);
        }
    }

    public function approve(Request $request, BOQApproval $approval): JsonResponse
    {
        $request->validate([
            'comments' => 'nullable|string',
            'approved_amount' => 'nullable|numeric|min:0',
            'conditions' => 'nullable|array',
        ]);

        try {
            DB::beginTransaction();

            // Set approver if not already set
            if (!$approval->approver_id) {
                $approver = Employee::first()?->id ?? 1; // Fallback
                $approval->update(['approver_id' => $approver]);
            }

            // Update approved amount if provided
            if ($request->has('approved_amount')) {
                $approval->update(['approved_amount' => $request->approved_amount]);
            }

            // Approve the step
            $approval->approve(
                $request->comments,
                $request->conditions
            );

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'BOQ approval completed successfully!',
                'approval' => $approval->fresh(['approver', 'boqVersion'])
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Failed to approve BOQ: ' . $e->getMessage()
            ], 500);
        }
    }

    public function reject(Request $request, BOQApproval $approval): JsonResponse
    {
        $request->validate([
            'comments' => 'required|string',
        ]);

        try {
            DB::beginTransaction();

            // Set approver if not already set
            if (!$approval->approver_id) {
                $approver = Employee::first()?->id ?? 1; // Fallback
                $approval->update(['approver_id' => $approver]);
            }

            $approval->reject($request->comments);

            // Update BOQ version status
            $approval->boqVersion?->update(['status' => 'Rejected']);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'BOQ rejected successfully!',
                'approval' => $approval->fresh(['approver', 'boqVersion'])
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Failed to reject BOQ: ' . $e->getMessage()
            ], 500);
        }
    }

    public function requestRevision(Request $request, BOQApproval $approval): JsonResponse
    {
        $request->validate([
            'comments' => 'required|string',
        ]);

        try {
            DB::beginTransaction();

            // Set approver if not already set
            if (!$approval->approver_id) {
                $approver = Employee::first()?->id ?? 1; // Fallback
                $approval->update(['approver_id' => $approver]);
            }

            $approval->requestRevision($request->comments);

            // Update BOQ version status
            $approval->boqVersion?->update(['status' => 'Revision Required']);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Revision requested successfully!',
                'approval' => $approval->fresh(['approver', 'boqVersion'])
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Failed to request revision: ' . $e->getMessage()
            ], 500);
        }
    }

    public function workflow(Project $project, BOQVersion $boqVersion = null): JsonResponse
    {
        try {
            $workflowData = BOQApproval::getWorkflowForProject(
                $project->id, 
                $boqVersion?->id
            );

            return response()->json([
                'success' => true,
                'workflow' => $workflowData
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to get approval workflow: ' . $e->getMessage()
            ], 500);
        }
    }

    public function history(Project $project): JsonResponse
    {
        try {
            $history = BOQApproval::where('project_id', $project->id)
                ->with(['boqVersion', 'approver'])
                ->whereIn('status', ['Approved', 'Rejected', 'Revision Required'])
                ->orderBy('reviewed_at', 'desc')
                ->get();

            return response()->json([
                'success' => true,
                'history' => $history
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to get approval history: ' . $e->getMessage()
            ], 500);
        }
    }

    public function pending(Request $request): JsonResponse
    {
        try {
            // Get pending approvals for current user (if we had auth)
            // For now, get all pending approvals
            $approvals = BOQApproval::where('status', 'Pending')
                ->with(['project', 'boqVersion', 'approver'])
                ->orderBy('submitted_at', 'asc')
                ->get();

            return response()->json([
                'success' => true,
                'pending_approvals' => $approvals
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to get pending approvals: ' . $e->getMessage()
            ], 500);
        }
    }

    public function dashboard(Project $project): JsonResponse
    {
        try {
            $currentVersion = $project->boqVersions()
                ->where('is_current', true)
                ->first();

            $stats = [
                'total_approvals' => BOQApproval::where('project_id', $project->id)->count(),
                'pending_approvals' => BOQApproval::where('project_id', $project->id)
                    ->where('status', 'Pending')->count(),
                'approved_count' => BOQApproval::where('project_id', $project->id)
                    ->where('status', 'Approved')->count(),
                'rejected_count' => BOQApproval::where('project_id', $project->id)
                    ->where('status', 'Rejected')->count(),
                'current_version' => $currentVersion,
                'latest_activity' => BOQApproval::where('project_id', $project->id)
                    ->whereNotNull('reviewed_at')
                    ->with(['approver', 'boqVersion'])
                    ->orderBy('reviewed_at', 'desc')
                    ->take(5)
                    ->get(),
            ];

            return response()->json([
                'success' => true,
                'dashboard' => $stats
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to get approval dashboard: ' . $e->getMessage()
            ], 500);
        }
    }
}