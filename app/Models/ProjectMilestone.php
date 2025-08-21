<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Carbon\Carbon;

class ProjectMilestone extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'project_id',
        'milestone_name',
        'description',
        'due_date',
        'completion_date',
        'status',
        'progress_percent',
        'is_critical',
        'assigned_to',
        'notes'
    ];

    protected $casts = [
        'due_date' => 'date',
        'completion_date' => 'date',
        'progress_percent' => 'integer',
        'is_critical' => 'boolean'
    ];

    // Relationships
    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    public function assignedTo(): BelongsTo
    {
        return $this->belongsTo(Employee::class, 'assigned_to');
    }

    // Accessors
    public function getDaysUntilDueAttribute(): int
    {
        if (!$this->due_date) return 0;
        return max(0, Carbon::now()->diffInDays($this->due_date, false));
    }

    public function getDaysOverdueAttribute(): int
    {
        if (!$this->due_date || $this->status === 'Completed') return 0;
        return max(0, Carbon::now()->diffInDays($this->due_date));
    }

    public function getIsOverdueAttribute(): bool
    {
        return $this->due_date && 
               $this->due_date->isPast() && 
               $this->status !== 'Completed';
    }

    public function getStatusColorAttribute(): string
    {
        return match($this->status) {
            'Completed' => 'green',
            'In Progress' => 'blue',
            'Overdue' => 'red',
            'Cancelled' => 'gray',
            default => 'yellow'
        };
    }

    public function getDurationDaysAttribute(): int
    {
        if (!$this->completion_date || !$this->due_date) return 0;
        return $this->due_date->diffInDays($this->completion_date);
    }

    // Scopes
    public function scopeCritical($query)
    {
        return $query->where('is_critical', true);
    }

    public function scopeOverdue($query)
    {
        return $query->where('due_date', '<', Carbon::now())
                    ->where('status', '!=', 'Completed');
    }

    public function scopeUpcoming($query, $days = 7)
    {
        return $query->whereBetween('due_date', [
            Carbon::now(),
            Carbon::now()->addDays($days)
        ])->where('status', '!=', 'Completed');
    }

    public function scopeByStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    // Methods
    public function markCompleted(): void
    {
        $this->status = 'Completed';
        $this->completion_date = Carbon::now();
        $this->progress_percent = 100;
        $this->save();
    }

    public function markInProgress(): void
    {
        $this->status = 'In Progress';
        $this->save();
    }

    public function updateProgress(int $percent): void
    {
        $this->progress_percent = min(100, max(0, $percent));
        
        if ($this->progress_percent === 100) {
            $this->markCompleted();
        } elseif ($this->progress_percent > 0 && $this->status === 'Planned') {
            $this->markInProgress();
        }
        
        $this->save();
    }

    protected static function boot()
    {
        parent::boot();

        static::saving(function ($milestone) {
            // Auto-update status based on progress and dates
            if ($milestone->progress_percent === 100 && $milestone->status !== 'Completed') {
                $milestone->status = 'Completed';
                $milestone->completion_date = $milestone->completion_date ?: Carbon::now();
            } elseif ($milestone->due_date && $milestone->due_date->isPast() && $milestone->status !== 'Completed') {
                $milestone->status = 'Overdue';
            }
        });
    }
}