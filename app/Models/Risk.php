<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Carbon\Carbon;

class Risk extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'project_id',
        'task_id',
        'risk_title',
        'description',
        'severity',
        'probability',
        'impact',
        'risk_score',
        'mitigation_plan',
        'contingency_plan',
        'status',
        'assigned_to',
        'target_mitigation_date',
        'actual_mitigation_date',
        'mitigation_cost',
        'notes'
    ];

    protected $casts = [
        'risk_score' => 'decimal:2',
        'mitigation_cost' => 'decimal:2',
        'target_mitigation_date' => 'date',
        'actual_mitigation_date' => 'date'
    ];

    protected $dates = [
        'target_mitigation_date',
        'actual_mitigation_date',
        'created_at',
        'updated_at',
        'deleted_at'
    ];

    // Relationships
    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    public function task(): BelongsTo
    {
        return $this->belongsTo(Task::class);
    }

    public function assignee(): BelongsTo
    {
        return $this->belongsTo(Employee::class, 'assigned_to');
    }

    // Accessors
    public function getRiskLevelAttribute(): string
    {
        if ($this->risk_score >= 15) return 'Critical';
        if ($this->risk_score >= 10) return 'High';
        if ($this->risk_score >= 5) return 'Medium';
        return 'Low';
    }

    public function getRiskLevelColorAttribute(): string
    {
        return match($this->risk_level) {
            'Critical' => 'error',
            'High' => 'warning',
            'Medium' => 'info',
            'Low' => 'success',
            default => 'default'
        };
    }

    public function getIsOverdueAttribute(): bool
    {
        if ($this->target_mitigation_date && $this->status !== 'Mitigated') {
            return $this->target_mitigation_date->isPast();
        }
        return false;
    }

    public function getDaysUntilMitigationAttribute(): int
    {
        if ($this->target_mitigation_date && $this->status !== 'Mitigated') {
            return Carbon::now()->diffInDays($this->target_mitigation_date, false);
        }
        return 0;
    }

    public function getMitigationDelayAttribute(): int
    {
        if ($this->target_mitigation_date && $this->actual_mitigation_date) {
            return $this->target_mitigation_date->diffInDays($this->actual_mitigation_date, false);
        }
        return 0;
    }

    public function getIsMitigatedAttribute(): bool
    {
        return $this->status === 'Mitigated';
    }

    public function getIsActiveAttribute(): bool
    {
        return in_array($this->status, ['Identified', 'Assessed', 'Monitored']);
    }

    public function getIsClosedAttribute(): bool
    {
        return $this->status === 'Closed';
    }

    // Scopes
    public function scopeByStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    public function scopeBySeverity($query, $severity)
    {
        return $query->where('severity', $severity);
    }

    public function scopeByProbability($query, $probability)
    {
        return $query->where('probability', $probability);
    }

    public function scopeByImpact($query, $impact)
    {
        return $query->where('impact', $impact);
    }

    public function scopeByProject($query, $projectId)
    {
        return $query->where('project_id', $projectId);
    }

    public function scopeByTask($query, $taskId)
    {
        return $query->where('task_id', $taskId);
    }

    public function scopeByAssignee($query, $assigneeId)
    {
        return $query->where('assigned_to', $assigneeId);
    }

    public function scopeCritical($query)
    {
        return $query->where('risk_score', '>=', 15);
    }

    public function scopeHigh($query)
    {
        return $query->where('risk_score', '>=', 10);
    }

    public function scopeOverdue($query)
    {
        return $query->where('target_mitigation_date', '<', Carbon::now())
                    ->whereNotIn('status', ['Mitigated', 'Closed']);
    }

    public function scopeActive($query)
    {
        return $query->whereIn('status', ['Identified', 'Assessed', 'Monitored']);
    }

    public function scopeByDateRange($query, $startDate, $endDate)
    {
        return $query->whereBetween('target_mitigation_date', [$startDate, $endDate]);
    }

    // Methods
    public function calculateRiskScore(): void
    {
        $severityScore = match($this->severity) {
            'Low' => 1,
            'Medium' => 2,
            'High' => 3,
            'Critical' => 4,
            default => 1
        };

        $probabilityScore = match($this->probability) {
            'Very Low' => 1,
            'Low' => 2,
            'Medium' => 3,
            'High' => 4,
            'Very High' => 5,
            default => 3
        };

        $impactScore = match($this->impact) {
            'Low' => 1,
            'Medium' => 2,
            'High' => 3,
            'Critical' => 4,
            default => 2
        };

        $this->risk_score = $severityScore * $probabilityScore * $impactScore;
        $this->save();
    }

    public function assignTo($employeeId): void
    {
        $this->assigned_to = $employeeId;
        $this->status = 'Assessed';
        $this->save();
    }

    public function startMitigation(): void
    {
        $this->status = 'Monitored';
        $this->save();
    }

    public function completeMitigation(): void
    {
        $this->status = 'Mitigated';
        $this->actual_mitigation_date = now();
        $this->save();
    }

    public function close(): void
    {
        $this->status = 'Closed';
        $this->save();
    }

    public function reopen(): void
    {
        $this->status = 'Identified';
        $this->actual_mitigation_date = null;
        $this->save();
    }

    public function updateTargetDate($date): void
    {
        $this->target_mitigation_date = $date;
        $this->save();
    }

    public function addMitigationCost($cost): void
    {
        $this->mitigation_cost = ($this->mitigation_cost ?? 0) + $cost;
        $this->save();
    }

    public function getMitigationProgress(): float
    {
        if ($this->status === 'Mitigated' || $this->status === 'Closed') {
            return 100;
        }
        if ($this->status === 'Monitored') {
            return 75;
        }
        if ($this->status === 'Assessed') {
            return 50;
        }
        return 25;
    }

    public function getNextAction(): string
    {
        return match($this->status) {
            'Identified' => 'Assess risk and assign to team member',
            'Assessed' => 'Develop and implement mitigation plan',
            'Monitored' => 'Monitor progress and complete mitigation',
            'Mitigated' => 'Verify effectiveness and close risk',
            'Closed' => 'Risk has been successfully managed',
            default => 'Review risk status'
        };
    }

    public function getPriorityLevel(): int
    {
        if ($this->risk_score >= 15) return 1; // Critical
        if ($this->risk_score >= 10) return 2; // High
        if ($this->risk_score >= 5) return 3;  // Medium
        return 4; // Low
    }

    public function canAssign(): bool
    {
        return $this->status === 'Identified';
    }

    public function canStartMitigation(): bool
    {
        return $this->status === 'Assessed' && $this->assigned_to;
    }

    public function canComplete(): bool
    {
        return $this->status === 'Monitored';
    }

    public function canClose(): bool
    {
        return $this->status === 'Mitigated';
    }

    public function canReopen(): bool
    {
        return in_array($this->status, ['Mitigated', 'Closed']);
    }

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($risk) {
            $risk->calculateRiskScore();
        });

        static::updating(function ($risk) {
            if ($risk->isDirty(['severity', 'probability', 'impact'])) {
                $risk->calculateRiskScore();
            }
        });
    }
}
