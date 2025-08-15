<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Carbon\Carbon;

class Resource extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'task_id',
        'employee_id',
        'equipment_id',
        'role',
        'allocated_hours',
        'actual_hours',
        'hourly_rate',
        'total_cost',
        'allocation_start_date',
        'allocation_end_date',
        'status',
        'notes'
    ];

    protected $casts = [
        'allocated_hours' => 'integer',
        'actual_hours' => 'integer',
        'hourly_rate' => 'decimal:2',
        'total_cost' => 'decimal:2',
        'allocation_start_date' => 'date',
        'allocation_end_date' => 'date'
    ];

    protected $dates = [
        'allocation_start_date',
        'allocation_end_date',
        'created_at',
        'updated_at',
        'deleted_at'
    ];

    // Relationships
    public function task(): BelongsTo
    {
        return $this->belongsTo(Task::class);
    }

    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class);
    }

    public function equipment(): BelongsTo
    {
        return $this->belongsTo(Equipment::class);
    }

    // Accessors
    public function getResourceNameAttribute(): string
    {
        if ($this->employee_id) {
            return $this->employee ? $this->employee->full_name : 'Unknown Employee';
        }
        if ($this->equipment_id) {
            return $this->equipment ? $this->equipment->name : 'Unknown Equipment';
        }
        return 'Unknown Resource';
    }

    public function getResourceTypeAttribute(): string
    {
        if ($this->employee_id) return 'Human';
        if ($this->equipment_id) return 'Equipment';
        return 'Unknown';
    }

    public function getDurationAttribute(): int
    {
        if ($this->allocation_start_date && $this->allocation_end_date) {
            return $this->allocation_start_date->diffInDays($this->allocation_end_date);
        }
        return 0;
    }

    public function getVarianceAttribute(): float
    {
        return $this->actual_hours - $this->allocated_hours;
    }

    public function getVariancePercentAttribute(): float
    {
        if ($this->allocated_hours > 0) {
            return ($this->variance / $this->allocated_hours) * 100;
        }
        return 0;
    }

    public function getIsOverallocatedAttribute(): bool
    {
        return $this->actual_hours > $this->allocated_hours;
    }

    public function getIsUnderallocatedAttribute(): bool
    {
        return $this->actual_hours < $this->allocated_hours;
    }

    public function getCostVarianceAttribute(): float
    {
        $plannedCost = $this->allocated_hours * ($this->hourly_rate ?? 0);
        return $this->total_cost - $plannedCost;
    }

    // Scopes
    public function scopeByStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    public function scopeByType($query, $type)
    {
        if ($type === 'human') {
            return $query->whereNotNull('employee_id');
        }
        if ($type === 'equipment') {
            return $query->whereNotNull('equipment_id');
        }
        return $query;
    }

    public function scopeByTask($query, $taskId)
    {
        return $query->where('task_id', $taskId);
    }

    public function scopeByEmployee($query, $employeeId)
    {
        return $query->where('employee_id', $employeeId);
    }

    public function scopeByEquipment($query, $equipmentId)
    {
        return $query->where('equipment_id', $equipmentId);
    }

    public function scopeActive($query)
    {
        return $query->whereIn('status', ['Allocated', 'Active']);
    }

    public function scopeOverallocated($query)
    {
        return $query->whereRaw('actual_hours > allocated_hours');
    }

    // Methods
    public function calculateTotalCost(): void
    {
        $this->total_cost = $this->actual_hours * ($this->hourly_rate ?? 0);
        $this->save();
    }

    public function updateStatus(): void
    {
        $today = Carbon::now();
        
        if ($this->allocation_start_date && $this->allocation_start_date->isFuture()) {
            $this->status = 'Allocated';
        } elseif ($this->allocation_end_date && $this->allocation_end_date->isPast()) {
            $this->status = 'Completed';
        } else {
            $this->status = 'Active';
        }
        
        $this->save();
    }

    public function isAvailableForDateRange($startDate, $endDate): bool
    {
        // Check if resource is available for the given date range
        if ($this->status === 'Completed') return false;
        
        // Check for date conflicts
        $conflict = static::where('id', '!=', $this->id)
            ->where(function ($query) use ($startDate, $endDate) {
                $query->whereBetween('allocation_start_date', [$startDate, $endDate])
                      ->orWhereBetween('allocation_end_date', [$startDate, $endDate])
                      ->orWhere(function ($q) use ($startDate, $endDate) {
                          $q->where('allocation_start_date', '<=', $startDate)
                            ->where('allocation_end_date', '>=', $endDate);
                      });
            })
            ->where('status', '!=', 'Cancelled')
            ->exists();
        
        return !$conflict;
    }

    public function getUtilizationRate(): float
    {
        if ($this->allocated_hours > 0) {
            return ($this->actual_hours / $this->allocated_hours) * 100;
        }
        return 0;
    }

    public function getEfficiencyScore(): float
    {
        if ($this->allocated_hours > 0 && $this->hourly_rate > 0) {
            $plannedCost = $this->allocated_hours * $this->hourly_rate;
            if ($plannedCost > 0) {
                return (($this->total_cost / $plannedCost) - 1) * 100;
            }
        }
        return 0;
    }

    protected static function boot()
    {
        parent::boot();

        static::updated(function ($resource) {
            // Update task actual cost when resource cost changes
            if ($resource->isDirty('total_cost')) {
                $resource->task->calculateActualCost();
            }
        });

        static::deleted(function ($resource) {
            // Update task actual cost when resource is deleted
            $resource->task->calculateActualCost();
        });
    }
}
