<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Carbon\Carbon;

class Employee extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'first_name',
        'last_name',
        'email',
        'employee_code',
        'position',
        'department',
        'hourly_rate',
        'is_active',
        'hire_date',
        'termination_date',
        'manager_id',
        'skills',
        'notes'
    ];

    protected $casts = [
        'hourly_rate' => 'decimal:2',
        'is_active' => 'boolean',
        'hire_date' => 'date',
        'termination_date' => 'date',
        'skills' => 'array'
    ];

    protected $dates = [
        'hire_date',
        'termination_date',
        'created_at',
        'updated_at',
        'deleted_at'
    ];

    // Relationships
    public function managedProjects(): HasMany
    {
        return $this->hasMany(Project::class, 'project_manager_id');
    }

    public function resources(): HasMany
    {
        return $this->hasMany(Resource::class);
    }

    public function documents(): HasMany
    {
        return $this->hasMany(Document::class, 'uploaded_by');
    }

    public function assignedRisks(): HasMany
    {
        return $this->hasMany(Risk::class, 'assigned_to');
    }

    public function manager(): BelongsTo
    {
        return $this->belongsTo(Employee::class, 'manager_id');
    }

    public function subordinates(): HasMany
    {
        return $this->hasMany(Employee::class, 'manager_id');
    }

    // Accessors
    public function getFullNameAttribute(): string
    {
        return "{$this->first_name} {$this->last_name}";
    }

    public function getInitialsAttribute(): string
    {
        return strtoupper(substr($this->first_name, 0, 1) . substr($this->last_name, 0, 1));
    }

    public function getTenureAttribute(): int
    {
        if ($this->hire_date) {
            return $this->hire_date->diffInYears(now());
        }
        return 0;
    }

    public function getCurrentHourlyRateAttribute(): float
    {
        return $this->hourly_rate ?? 0;
    }

    public function getActiveProjectsCountAttribute(): int
    {
        return $this->managedProjects()->active()->count();
    }

    public function getTotalAllocatedHoursAttribute(): int
    {
        return $this->resources()->active()->sum('allocated_hours');
    }

    public function getTotalActualHoursAttribute(): int
    {
        return $this->resources()->active()->sum('actual_hours');
    }

    public function getUtilizationRateAttribute(): float
    {
        $allocated = $this->total_allocated_hours;
        if ($allocated > 0) {
            return ($this->total_actual_hours / $allocated) * 100;
        }
        return 0;
    }

    public function getIsAvailableAttribute(): bool
    {
        return $this->is_active && !$this->termination_date;
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeByDepartment($query, $department)
    {
        return $query->where('department', $department);
    }

    public function scopeByPosition($query, $position)
    {
        return $query->where('position', $position);
    }

    public function scopeAvailable($query)
    {
        return $query->where('is_active', true)
                    ->whereNull('termination_date');
    }

    public function scopeBySkill($query, $skill)
    {
        return $query->whereJsonContains('skills', $skill);
    }

    public function scopeSearch($query, $search)
    {
        return $query->where(function ($q) use ($search) {
            $q->where('first_name', 'like', "%{$search}%")
              ->orWhere('last_name', 'like', "%{$search}%")
              ->orWhere('email', 'like', "%{$search}%")
              ->orWhere('employee_code', 'like', "%{$search}%");
        });
    }

    // Methods
    public function isAvailableForDateRange($startDate, $endDate): bool
    {
        // Check if employee is available for the given date range
        if (!$this->is_available) return false;
        
        // Check for resource allocation conflicts
        $conflict = $this->resources()
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

    public function getAvailabilityForDateRange($startDate, $endDate): array
    {
        $allocations = $this->resources()
            ->whereBetween('allocation_start_date', [$startDate, $endDate])
            ->orWhereBetween('allocation_end_date', [$startDate, $endDate])
            ->orWhere(function ($query) use ($startDate, $endDate) {
                $query->where('allocation_start_date', '<=', $startDate)
                      ->where('allocation_end_date', '>=', $endDate);
            })
            ->get();

        $availability = [];
        $currentDate = Carbon::parse($startDate);
        $endDate = Carbon::parse($endDate);

        while ($currentDate <= $endDate) {
            $date = $currentDate->format('Y-m-d');
            $allocatedHours = $allocations
                ->filter(function ($allocation) use ($date) {
                    return $allocation->allocation_start_date <= $date && 
                           $allocation->allocation_end_date >= $date;
                })
                ->sum('allocated_hours');

            $availability[$date] = [
                'date' => $date,
                'allocated_hours' => $allocatedHours,
                'available_hours' => 8 - $allocatedHours, // Assuming 8-hour workday
                'is_available' => $allocatedHours < 8
            ];

            $currentDate->addDay();
        }

        return $availability;
    }

    public function getWorkloadForPeriod($startDate, $endDate): array
    {
        $resources = $this->resources()
            ->whereBetween('allocation_start_date', [$startDate, $endDate])
            ->orWhereBetween('allocation_end_date', [$startDate, $endDate])
            ->orWhere(function ($query) use ($startDate, $endDate) {
                $query->where('allocation_start_date', '<=', $startDate)
                      ->where('allocation_end_date', '>=', $endDate);
            })
            ->with(['task.project'])
            ->get();

        $workload = [
            'total_allocated_hours' => $resources->sum('allocated_hours'),
            'total_actual_hours' => $resources->sum('actual_hours'),
            'projects_count' => $resources->pluck('task.project_id')->unique()->count(),
            'tasks_count' => $resources->pluck('task_id')->unique()->count(),
            'utilization_rate' => 0,
            'projects' => []
        ];

        if ($workload['total_allocated_hours'] > 0) {
            $workload['utilization_rate'] = ($workload['total_actual_hours'] / $workload['total_allocated_hours']) * 100;
        }

        // Group by projects
        $projectGroups = $resources->groupBy('task.project_id');
        foreach ($projectGroups as $projectId => $projectResources) {
            $project = $projectResources->first()->task->project;
            $workload['projects'][] = [
                'id' => $projectId,
                'name' => $project->project_name,
                'code' => $project->project_code,
                'allocated_hours' => $projectResources->sum('allocated_hours'),
                'actual_hours' => $projectResources->sum('actual_hours'),
                'tasks_count' => $projectResources->pluck('task_id')->unique()->count()
            ];
        }

        return $workload;
    }

    public function hasSkill($skill): bool
    {
        return in_array($skill, $this->skills ?? []);
    }

    public function addSkill($skill): void
    {
        $skills = $this->skills ?? [];
        if (!in_array($skill, $skills)) {
            $skills[] = $skill;
            $this->skills = $skills;
            $this->save();
        }
    }

    public function removeSkill($skill): void
    {
        $skills = $this->skills ?? [];
        $skills = array_filter($skills, fn($s) => $s !== $skill);
        $this->skills = array_values($skills);
        $this->save();
    }
}
