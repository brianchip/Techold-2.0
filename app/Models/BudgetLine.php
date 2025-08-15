<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Carbon\Carbon;

class BudgetLine extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'project_id',
        'task_id',
        'category',
        'description',
        'unit',
        'quantity',
        'unit_cost',
        'planned_amount',
        'actual_amount',
        'variance',
        'variance_percent',
        'status',
        'boq_reference',
        'supplier_reference',
        'planned_date',
        'actual_date',
        'notes'
    ];

    protected $casts = [
        'quantity' => 'decimal:4',
        'unit_cost' => 'decimal:4',
        'planned_amount' => 'decimal:2',
        'actual_amount' => 'decimal:2',
        'variance' => 'decimal:2',
        'variance_percent' => 'decimal:2',
        'planned_date' => 'date',
        'actual_date' => 'date'
    ];

    protected $dates = [
        'planned_date',
        'actual_date',
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

    // Accessors
    public function getIsOverBudgetAttribute(): bool
    {
        return $this->actual_amount > $this->planned_amount;
    }

    public function getIsUnderBudgetAttribute(): bool
    {
        return $this->actual_amount < $this->planned_amount;
    }

    public function getBudgetStatusAttribute(): string
    {
        if ($this->is_over_budget) return 'Over Budget';
        if ($this->is_under_budget) return 'Under Budget';
        return 'On Budget';
    }

    public function getBudgetStatusColorAttribute(): string
    {
        if ($this->is_over_budget) return 'error';
        if ($this->is_under_budget) return 'success';
        return 'info';
    }

    public function getEfficiencyScoreAttribute(): float
    {
        if ($this->planned_amount > 0) {
            return (($this->actual_amount / $this->planned_amount) - 1) * 100;
        }
        return 0;
    }

    public function getIsDelayedAttribute(): bool
    {
        if ($this->planned_date && $this->actual_date) {
            return $this->actual_date->isAfter($this->planned_date);
        }
        if ($this->planned_date && !$this->actual_date) {
            return $this->planned_date->isPast();
        }
        return false;
    }

    // Scopes
    public function scopeByCategory($query, $category)
    {
        return $query->where('category', $category);
    }

    public function scopeByStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    public function scopeByProject($query, $projectId)
    {
        return $query->where('project_id', $projectId);
    }

    public function scopeByTask($query, $taskId)
    {
        return $query->where('task_id', $taskId);
    }

    public function scopeOverBudget($query)
    {
        return $query->whereRaw('actual_amount > planned_amount');
    }

    public function scopeUnderBudget($query)
    {
        return $query->whereRaw('actual_amount < planned_amount');
    }

    public function scopeDelayed($query)
    {
        return $query->where('planned_date', '<', Carbon::now())
                    ->whereNull('actual_date');
    }

    public function scopeByDateRange($query, $startDate, $endDate)
    {
        return $query->whereBetween('planned_date', [$startDate, $endDate]);
    }

    // Methods
    public function calculateVariance(): void
    {
        $this->variance = $this->actual_amount - $this->planned_amount;
        
        if ($this->planned_amount > 0) {
            $this->variance_percent = ($this->variance / $this->planned_amount) * 100;
        } else {
            $this->variance_percent = 0;
        }
        
        $this->save();
    }

    public function updateActualAmount($amount): void
    {
        $this->actual_amount = $amount;
        $this->calculateVariance();
        
        // Update project actual cost
        $this->project->calculateActualCost();
        
        // Update task actual cost if linked
        if ($this->task_id) {
            $this->task->calculateActualCost();
        }
    }

    public function getCategoryIcon(): string
    {
        return match($this->category) {
            'Material' => 'inventory',
            'Labor' => 'people',
            'Overhead' => 'business',
            'Equipment' => 'build',
            'Subcontractor' => 'handshake',
            'Other' => 'more_horiz',
            default => 'attach_money'
        };
    }

    public function getCategoryColor(): string
    {
        return match($this->category) {
            'Material' => 'primary',
            'Labor' => 'secondary',
            'Overhead' => 'warning',
            'Equipment' => 'info',
            'Subcontractor' => 'success',
            'Other' => 'default',
            default => 'primary'
        };
    }

    public function getStatusColor(): string
    {
        return match($this->status) {
            'Planned' => 'info',
            'Approved' => 'success',
            'In Progress' => 'primary',
            'Completed' => 'success',
            'Cancelled' => 'error',
            default => 'default'
        };
    }

    public function canApprove(): bool
    {
        return $this->status === 'Planned';
    }

    public function canStart(): bool
    {
        return in_array($this->status, ['Planned', 'Approved']);
    }

    public function canComplete(): bool
    {
        return $this->status === 'In Progress';
    }

    public function canCancel(): bool
    {
        return in_array($this->status, ['Planned', 'Approved', 'In Progress']);
    }

    protected static function boot()
    {
        parent::boot();

        static::updated(function ($budgetLine) {
            // Update project actual cost when budget line changes
            if ($budgetLine->isDirty('actual_amount')) {
                $budgetLine->project->calculateActualCost();
            }
        });

        static::deleted(function ($budgetLine) {
            // Update project actual cost when budget line is deleted
            $budgetLine->project->calculateActualCost();
        });
    }
}
