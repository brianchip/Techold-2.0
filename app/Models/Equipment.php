<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Equipment extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'equipment_code',
        'type',
        'model',
        'serial_number',
        'hourly_rate',
        'is_available',
        'location',
        'purchase_date',
        'warranty_expiry',
        'maintenance_schedule',
        'last_maintenance',
        'next_maintenance',
        'status',
        'notes'
    ];

    protected $casts = [
        'hourly_rate' => 'decimal:2',
        'is_available' => 'boolean',
        'purchase_date' => 'date',
        'warranty_expiry' => 'date',
        'last_maintenance' => 'date',
        'next_maintenance' => 'date',
        'maintenance_schedule' => 'array'
    ];

    protected $dates = [
        'purchase_date',
        'warranty_expiry',
        'last_maintenance',
        'next_maintenance',
        'created_at',
        'updated_at',
        'deleted_at'
    ];

    // Relationships
    public function resources(): HasMany
    {
        return $this->hasMany(Resource::class);
    }

    // Accessors
    public function getFullNameAttribute(): string
    {
        if ($this->model) {
            return "{$this->name} - {$this->model}";
        }
        return $this->name;
    }

    public function getAgeAttribute(): int
    {
        if ($this->purchase_date) {
            return $this->purchase_date->diffInYears(now());
        }
        return 0;
    }

    public function getWarrantyStatusAttribute(): string
    {
        if (!$this->warranty_expiry) return 'No Warranty';
        
        if ($this->warranty_expiry->isPast()) {
            return 'Expired';
        }
        
        if ($this->warranty_expiry->diffInDays(now()) <= 30) {
            return 'Expiring Soon';
        }
        
        return 'Active';
    }

    public function getMaintenanceStatusAttribute(): string
    {
        if (!$this->next_maintenance) return 'No Schedule';
        
        if ($this->next_maintenance->isPast()) {
            return 'Overdue';
        }
        
        if ($this->next_maintenance->diffInDays(now()) <= 7) {
            return 'Due Soon';
        }
        
        return 'Scheduled';
    }

    public function getUtilizationRateAttribute(): float
    {
        $totalHours = $this->resources()->sum('allocated_hours');
        $maxHours = 24 * 30; // Assuming 24/7 availability for equipment
        
        if ($maxHours > 0) {
            return ($totalHours / $maxHours) * 100;
        }
        
        return 0;
    }

    public function getCurrentHourlyRateAttribute(): float
    {
        return $this->hourly_rate ?? 0;
    }

    public function getActiveAllocationsCountAttribute(): int
    {
        return $this->resources()->active()->count();
    }

    public function getTotalAllocatedHoursAttribute(): int
    {
        return $this->resources()->active()->sum('allocated_hours');
    }

    public function getTotalActualHoursAttribute(): int
    {
        return $this->resources()->active()->sum('actual_hours');
    }

    public function getIsOperationalAttribute(): bool
    {
        return $this->is_available && $this->status === 'operational';
    }

    public function getIsUnderMaintenanceAttribute(): bool
    {
        return $this->status === 'maintenance';
    }

    public function getIsOutOfServiceAttribute(): bool
    {
        return $this->status === 'out_of_service';
    }

    // Scopes
    public function scopeAvailable($query)
    {
        return $query->where('is_available', true)
                    ->where('status', 'operational');
    }

    public function scopeByType($query, $type)
    {
        return $query->where('type', $type);
    }

    public function scopeByStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    public function scopeByLocation($query, $location)
    {
        return $query->where('location', $location);
    }

    public function scopeOperational($query)
    {
        return $query->where('status', 'operational');
    }

    public function scopeUnderMaintenance($query)
    {
        return $query->where('status', 'maintenance');
    }

    public function scopeOutOfService($query)
    {
        return $query->where('status', 'out_of_service');
    }

    public function scopeSearch($query, $search)
    {
        return $query->where(function ($q) use ($search) {
            $q->where('name', 'like', "%{$search}%")
              ->orWhere('equipment_code', 'like', "%{$search}%")
              ->orWhere('model', 'like', "%{$search}%")
              ->orWhere('serial_number', 'like', "%{$search}%");
        });
    }

    // Methods
    public function isAvailableForDateRange($startDate, $endDate): bool
    {
        // Check if equipment is available for the given date range
        if (!$this->is_operational) return false;
        
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
                'available_hours' => 24 - $allocatedHours, // Assuming 24/7 availability
                'is_available' => $allocatedHours < 24
            ];

            $currentDate->addDay();
        }

        return $availability;
    }

    public function scheduleMaintenance($date, $duration = 1): void
    {
        $this->next_maintenance = $date;
        $this->status = 'maintenance';
        $this->save();
    }

    public function completeMaintenance(): void
    {
        $this->last_maintenance = now();
        $this->status = 'operational';
        
        // Calculate next maintenance date based on schedule
        if ($this->maintenance_schedule) {
            $interval = $this->maintenance_schedule['interval'] ?? 30; // Default 30 days
            $this->next_maintenance = now()->addDays($interval);
        }
        
        $this->save();
    }

    public function markOutOfService($reason = null): void
    {
        $this->status = 'out_of_service';
        $this->notes = $reason ? ($this->notes . "\nOut of Service: " . $reason) : $this->notes;
        $this->save();
    }

    public function returnToService(): void
    {
        $this->status = 'operational';
        $this->save();
    }

    public function calculateDepreciation(): float
    {
        if (!$this->purchase_date || !$this->hourly_rate) return 0;
        
        $age = $this->age;
        $depreciationRate = 0.1; // 10% per year
        
        return $this->hourly_rate * (1 - ($depreciationRate * $age));
    }

    public function getMaintenanceHistory(): array
    {
        // This would typically come from a maintenance log table
        // For now, return basic information
        return [
            'last_maintenance' => $this->last_maintenance,
            'next_maintenance' => $this->next_maintenance,
            'maintenance_schedule' => $this->maintenance_schedule,
            'status' => $this->maintenance_status
        ];
    }

    public function getOperationalMetrics(): array
    {
        $currentMonth = now()->startOfMonth();
        $lastMonth = now()->subMonth()->startOfMonth();
        
        $currentMonthHours = $this->resources()
            ->whereMonth('allocation_start_date', $currentMonth->month)
            ->whereYear('allocation_start_date', $currentMonth->year)
            ->sum('allocated_hours');
            
        $lastMonthHours = $this->resources()
            ->whereMonth('allocation_start_date', $lastMonth->month)
            ->whereYear('allocation_start_date', $lastMonth->year)
            ->sum('allocated_hours');
        
        return [
            'current_month_hours' => $currentMonthHours,
            'last_month_hours' => $lastMonthHours,
            'utilization_rate' => $this->utilization_rate,
            'total_allocated_hours' => $this->total_allocated_hours,
            'total_actual_hours' => $this->total_actual_hours,
            'active_allocations' => $this->active_allocations_count
        ];
    }

    protected static function boot()
    {
        parent::boot();

        static::updating(function ($equipment) {
            // Update availability based on status
            if ($equipment->isDirty('status')) {
                $equipment->is_available = in_array($equipment->status, ['operational', 'available']);
            }
        });
    }
}
