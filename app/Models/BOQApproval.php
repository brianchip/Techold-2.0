<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BOQApproval extends Model
{
    use HasFactory;

    protected $table = 'boq_approvals';

    protected $fillable = [
        'project_id',
        'boq_version_id',
        'approval_type',
        'status',
        'approver_id',
        'comments',
        'approval_data',
        'submitted_at',
        'reviewed_at',
        'approval_order',
        'is_required',
        'approved_amount',
        'approval_conditions',
    ];

    protected $casts = [
        'approval_data' => 'array',
        'approval_conditions' => 'array',
        'submitted_at' => 'datetime',
        'reviewed_at' => 'datetime',
        'approved_amount' => 'decimal:2',
        'is_required' => 'boolean',
    ];

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    public function boqVersion(): BelongsTo
    {
        return $this->belongsTo(BOQVersion::class);
    }

    public function approver(): BelongsTo
    {
        return $this->belongsTo(Employee::class, 'approver_id');
    }

    // Approve this approval step
    public function approve(string $comments = null, array $conditions = null): void
    {
        $this->update([
            'status' => 'Approved',
            'comments' => $comments,
            'approval_conditions' => $conditions,
            'reviewed_at' => now(),
        ]);
        
        // Check if all required approvals are complete
        $this->checkAndCompleteWorkflow();
    }

    // Reject this approval step
    public function reject(string $comments): void
    {
        $this->update([
            'status' => 'Rejected',
            'comments' => $comments,
            'reviewed_at' => now(),
        ]);
    }

    // Request revision
    public function requestRevision(string $comments): void
    {
        $this->update([
            'status' => 'Revision Required',
            'comments' => $comments,
            'reviewed_at' => now(),
        ]);
    }

    // Check if approval workflow is complete
    private function checkAndCompleteWorkflow(): void
    {
        $pendingApprovals = static::where('project_id', $this->project_id)
            ->where('boq_version_id', $this->boq_version_id)
            ->where('is_required', true)
            ->where('status', 'Pending')
            ->count();
            
        if ($pendingApprovals === 0) {
            // All approvals complete - mark BOQ version as approved
            $this->boqVersion?->update([
                'status' => 'Approved',
                'approved_at' => now(),
            ]);
        }
    }

    // Get approval workflow for a project
    public static function getWorkflowForProject(int $projectId, int $boqVersionId = null): array
    {
        $query = static::where('project_id', $projectId)
            ->orderBy('approval_order');
            
        if ($boqVersionId) {
            $query->where('boq_version_id', $boqVersionId);
        }
        
        return $query->with('approver')->get()->toArray();
    }

    // Create standard approval workflow for a project
    public static function createStandardWorkflow(int $projectId, int $boqVersionId, float $totalAmount): void
    {
        $approvals = [
            [
                'approval_type' => 'Engineering Manager',
                'approval_order' => 1,
                'is_required' => true,
            ],
            [
                'approval_type' => 'Finance Manager',
                'approval_order' => 2,
                'is_required' => true,
            ],
        ];
        
        // Add Managing Director approval for amounts above $10,000
        if ($totalAmount > 10000) {
            $approvals[] = [
                'approval_type' => 'Managing Director',
                'approval_order' => 3,
                'is_required' => true,
            ];
        }
        
        foreach ($approvals as $approval) {
            static::create(array_merge($approval, [
                'project_id' => $projectId,
                'boq_version_id' => $boqVersionId,
                'status' => 'Pending',
                'submitted_at' => now(),
            ]));
        }
    }

    public function getStatusColorAttribute(): string
    {
        return match($this->status) {
            'Approved' => 'green',
            'Rejected' => 'red',
            'Revision Required' => 'yellow',
            default => 'gray',
        };
    }

    public function getStatusIconAttribute(): string
    {
        return match($this->status) {
            'Approved' => 'fas fa-check-circle',
            'Rejected' => 'fas fa-times-circle',
            'Revision Required' => 'fas fa-exclamation-triangle',
            default => 'fas fa-clock',
        };
    }
}