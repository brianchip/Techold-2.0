<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProjectApproval extends Model
{
    use HasFactory;

    protected $fillable = [
        'project_id',
        'approval_type',
        'approver_role',
        'approver_id',
        'status',
        'comments',
        'approved_amount',
        'submitted_at',
        'responded_at',
        'approval_data'
    ];

    protected $casts = [
        'submitted_at' => 'datetime',
        'responded_at' => 'datetime',
        'approved_amount' => 'decimal:2',
        'approval_data' => 'array'
    ];

    /**
     * Get the project that owns the approval
     */
    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    /**
     * Get the approver (employee)
     */
    public function approver(): BelongsTo
    {
        return $this->belongsTo(Employee::class, 'approver_id');
    }

    /**
     * Check if approval is pending
     */
    public function isPending(): bool
    {
        return $this->status === 'Pending';
    }

    /**
     * Check if approval is approved
     */
    public function isApproved(): bool
    {
        return $this->status === 'Approved';
    }

    /**
     * Check if approval is rejected
     */
    public function isRejected(): bool
    {
        return $this->status === 'Rejected';
    }

    /**
     * Get response time in hours
     */
    public function getResponseTimeHoursAttribute(): ?float
    {
        if (!$this->responded_at) {
            return null;
        }
        
        return $this->submitted_at->diffInHours($this->responded_at);
    }

    /**
     * Scope for pending approvals
     */
    public function scopePending($query)
    {
        return $query->where('status', 'Pending');
    }

    /**
     * Scope for approved approvals
     */
    public function scopeApproved($query)
    {
        return $query->where('status', 'Approved');
    }

    /**
     * Scope for specific approval types
     */
    public function scopeOfType($query, string $type)
    {
        return $query->where('approval_type', $type);
    }

    /**
     * Scope for specific approver roles
     */
    public function scopeForRole($query, string $role)
    {
        return $query->where('approver_role', $role);
    }
}