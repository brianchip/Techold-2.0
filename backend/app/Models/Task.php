<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Carbon\Carbon;

class Task extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'project_id',
        'parent_task_id',
        'task_name',
        'description',
        'start_date',
        'end_date',
        'planned_cost',
        'actual_cost',
        'progress_percent',
        'dependency_type',
        'dependency_task_id',
        'status',
        'priority',
        'estimated_hours',
        'actual_hours',
        'metadata'
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'planned_cost' => 'decimal:2',
        'actual_cost' => 'decimal:2',
        'progress_percent' => 'integer',
        'priority' => 'integer',
        'estimated_hours' => 'integer',
        'actual_hours' => 'integer',
        'metadata' => 'array'
    ];

    protected $dates = [
        'start_date',
        'end_date',
        'created_at',
        'updated_at',
        'deleted_at'
    ];

    // Relationships
    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    public function parentTask(): BelongsTo
    {
        return $this->belongsTo(Task::class, 'parent_task_id');
    }

    public function subTasks(): HasMany
    {
        return $this->hasMany(Task::class, 'parent_task_id');
    }

    public function dependencyTask(): BelongsTo
    {
        return $this->belongsTo(Task::class, 'dependency_task_id');
    }

    public function resources(): HasMany
    {
        return $this->hasMany(Resource::class);
    }

    public function documents(): HasMany
    {
        return $this->hasMany(Document::class);
    }

    // Accessors
    public function getDurationAttribute(): int
    {
        if ($this->start_date && $this->end_date) {
            return $this->start_date->diffInDays($this->end_date);
        }
        return 0;
    }

    public function getVarianceAttribute(): float
    {
        return $this->actual_cost - $this->planned_cost;
    }

    public function getVariancePercentAttribute(): float
    {
        if ($this->planned_cost > 0) {
            return ($this->variance / $this->planned_cost) * 100;
        }
        return 0;
    }

    public function getIsOverdueAttribute(): bool
    {
        if ($this->status === 'In Progress' && $this->end_date) {
            return $this->end_date->isPast();
        }
        return false;
    }

    public function getDaysRemainingAttribute(): int
    {
        if ($this->end_date && $this->status === 'In Progress') {
            return max(0, Carbon::now()->diffInDays($this->end_date, false));
        }
        return 0;
    }

    public function getIsCriticalAttribute(): bool
    {
        return $this->priority === 1 || $this->is_overdue;
    }

    // Scopes
    public function scopeByStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    public function scopeByPriority($query, $priority)
    {
        return $query->where('priority', $priority);
    }

    public function scopeOverdue($query)
    {
        return $query->where('status', 'In Progress')
                    ->where('end_date', '<', Carbon::now());
    }

    public function scopeCritical($query)
    {
        return $query->where('priority', 1)
                    ->orWhere(function ($q) {
                        $q->where('status', 'In Progress')
                          ->where('end_date', '<', Carbon::now());
                    });
    }

    public function scopeByProject($query, $projectId)
    {
        return $query->where('project_id', $projectId);
    }

    public function scopeRootTasks($query)
    {
        return $query->whereNull('parent_task_id');
    }

    // Methods
    public function updateProgress(): void
    {
        if ($this->subTasks()->count() > 0) {
            $totalProgress = $this->subTasks()->avg('progress_percent');
            $this->update(['progress_percent' => round($totalProgress)]);
        }
    }

    public function calculateActualCost(): void
    {
        $actualCost = $this->resources()->sum('total_cost');
        $this->update(['actual_cost' => $actualCost]);
    }

    public function canStart(): bool
    {
        if ($this->dependency_task_id) {
            $dependency = $this->dependencyTask;
            if (!$dependency) return false;

            switch ($this->dependency_type) {
                case 'FS': // Finish-Start
                    return $dependency->status === 'Completed';
                case 'SS': // Start-Start
                    return $dependency->status === 'In Progress' || $dependency->status === 'Completed';
                case 'FF': // Finish-Finish
                    return $dependency->status === 'Completed';
                case 'SF': // Start-Finish
                    return $dependency->status === 'In Progress' || $dependency->status === 'Completed';
                default:
                    return true;
            }
        }
        return true;
    }

    public function getDependencyDescription(): string
    {
        if (!$this->dependency_task_id) return 'No dependencies';

        $dependency = $this->dependencyTask;
        if (!$dependency) return 'Invalid dependency';

        $typeMap = [
            'FS' => 'Finish-Start',
            'SS' => 'Start-Start',
            'FF' => 'Finish-Finish',
            'SF' => 'Start-Finish'
        ];

        $type = $typeMap[$this->dependency_type] ?? $this->dependency_type;
        return "{$type}: {$dependency->task_name}";
    }

    public function getPriorityLabel(): string
    {
        return match($this->priority) {
            1 => 'High',
            2 => 'Medium',
            3 => 'Low',
            default => 'Unknown'
        };
    }

    public function getStatusColor(): string
    {
        return match($this->status) {
            'Completed' => 'success',
            'In Progress' => 'primary',
            'Not Started' => 'info',
            'On Hold' => 'warning',
            'Cancelled' => 'error',
            default => 'default'
        };
    }

    protected static function boot()
    {
        parent::boot();

        static::updated(function ($task) {
            // Update parent task progress if this task's progress changed
            if ($task->isDirty('progress_percent') && $task->parent_task_id) {
                $task->parentTask->updateProgress();
            }

            // Update project progress
            $task->project->updateProgress();
        });

        static::deleted(function ($task) {
            // Update parent task progress if this task was deleted
            if ($task->parent_task_id) {
                $task->parentTask->updateProgress();
            }

            // Update project progress
            $task->project->updateProgress();
        });
    }
}
