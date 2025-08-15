<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Carbon\Carbon;

class Project extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'project_code',
        'project_name',
        'project_type',
        'client_id',
        'start_date',
        'end_date',
        'status',
        'project_manager_id',
        'description',
        'total_budget',
        'actual_cost',
        'progress_percent',
        'location',
        'metadata'
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'total_budget' => 'decimal:2',
        'actual_cost' => 'decimal:2',
        'progress_percent' => 'integer',
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
    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }

    public function projectManager(): BelongsTo
    {
        return $this->belongsTo(Employee::class, 'project_manager_id');
    }

    public function tasks(): HasMany
    {
        return $this->hasMany(Task::class);
    }

    public function resources(): HasMany
    {
        return $this->hasMany(Resource::class);
    }

    public function budgetLines(): HasMany
    {
        return $this->hasMany(BudgetLine::class);
    }

    public function documents(): HasMany
    {
        return $this->hasMany(Document::class);
    }

    public function risks(): HasMany
    {
        return $this->hasMany(Risk::class);
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
        return $this->actual_cost - $this->total_budget;
    }

    public function getVariancePercentAttribute(): float
    {
        if ($this->total_budget > 0) {
            return ($this->variance / $this->total_budget) * 100;
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

    // Scopes
    public function scopeActive($query)
    {
        return $query->whereIn('status', ['Planned', 'In Progress']);
    }

    public function scopeByStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    public function scopeByType($query, $type)
    {
        return $query->where('project_type', $type);
    }

    public function scopeByManager($query, $managerId)
    {
        return $query->where('project_manager_id', $managerId);
    }

    public function scopeOverdue($query)
    {
        return $query->where('status', 'In Progress')
                    ->where('end_date', '<', Carbon::now());
    }

    // Methods
    public function generateProjectCode(): string
    {
        $prefix = strtoupper(substr($this->project_type, 0, 3));
        $year = date('Y');
        $sequence = static::where('project_type', $this->project_type)
                          ->whereYear('created_at', $year)
                          ->count() + 1;
        
        return sprintf('%s-%s-%04d', $prefix, $year, $sequence);
    }

    public function updateProgress(): void
    {
        if ($this->tasks()->count() > 0) {
            $totalProgress = $this->tasks()->avg('progress_percent');
            $this->update(['progress_percent' => round($totalProgress)]);
        }
    }

    public function calculateActualCost(): void
    {
        $actualCost = $this->budgetLines()->sum('actual_amount');
        $this->update(['actual_cost' => $actualCost]);
    }

    public function createProjectFolderStructure(): void
    {
        $folders = [
            '01.Contracts & BOQs',
            '02.Design & Drawings',
            '03.Site Surveys',
            '04.Procurement & Invoices',
            '05.Progress Reports',
            '06.SHEQ',
            '07.Photos & Media',
            '08.Meeting Minutes'
        ];

        $basePath = "projects/{$this->project_code} - {$this->project_name}";
        
        foreach ($folders as $folder) {
            $fullPath = storage_path("app/public/{$basePath}/{$folder}");
            if (!file_exists($fullPath)) {
                mkdir($fullPath, 0755, true);
            }
        }
    }

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($project) {
            if (empty($project->project_code)) {
                $project->project_code = $project->generateProjectCode();
            }
        });

        static::created(function ($project) {
            $project->createProjectFolderStructure();
        });
    }
}
