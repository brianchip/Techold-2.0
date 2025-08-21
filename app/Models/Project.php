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
        'costing_type',
        'client_id',
        'start_date',
        'end_date',
        'status',
        'project_manager_id',
        'prime_mover_id',
        'description',
        'total_budget',
        'actual_cost',
        'progress_percent',
        'location',
        'metadata',
        // Approval workflow
        'engineering_manager_approved',
        'engineering_manager_approved_at',
        'engineering_manager_id',
        'finance_manager_approved',
        'finance_manager_approved_at',
        'finance_manager_id',
        'md_approved',
        'md_approved_at',
        'md_id',
        // SAP integration
        'sap_project_code',
        'procurement_budget',
        'actual_procurement_cost',
        // Variance tracking
        'budget_variance',
        'budget_variance_percent',
        // Project closeout
        'is_closed_out',
        'closed_out_at',
        'closeout_notes',
        // Emergency procurement
        'emergency_procurement',
        'emergency_justification'
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'total_budget' => 'decimal:2',
        'actual_cost' => 'decimal:2',
        'progress_percent' => 'integer',
        'metadata' => 'array',
        // Approval workflow casts
        'engineering_manager_approved' => 'boolean',
        'engineering_manager_approved_at' => 'datetime',
        'finance_manager_approved' => 'boolean',
        'finance_manager_approved_at' => 'datetime',
        'md_approved' => 'boolean',
        'md_approved_at' => 'datetime',
        // Financial casts
        'procurement_budget' => 'decimal:2',
        'actual_procurement_cost' => 'decimal:2',
        'budget_variance' => 'decimal:2',
        'budget_variance_percent' => 'decimal:2',
        // Closeout casts
        'is_closed_out' => 'boolean',
        'closed_out_at' => 'datetime',
        'emergency_procurement' => 'boolean'
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

    public function resources()
    {
        return $this->hasManyThrough(Resource::class, Task::class);
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

    // New costing workflow relationships
    public function primeMover(): BelongsTo
    {
        return $this->belongsTo(Employee::class, 'prime_mover_id');
    }

    public function engineeringManager(): BelongsTo
    {
        return $this->belongsTo(Employee::class, 'engineering_manager_id');
    }

    public function financeManager(): BelongsTo
    {
        return $this->belongsTo(Employee::class, 'finance_manager_id');
    }

    public function managingDirector(): BelongsTo
    {
        return $this->belongsTo(Employee::class, 'md_id');
    }

    public function quotes(): HasMany
    {
        return $this->hasMany(ProjectQuote::class);
    }

    public function approvals(): HasMany
    {
        return $this->hasMany(ProjectApproval::class);
    }

    // New comprehensive module relationships
    public function boqSections(): HasMany
    {
        return $this->hasMany(BOQSection::class);
    }

    public function boqItems(): HasMany
    {
        return $this->hasMany(BOQItem::class);
    }

    public function milestones(): HasMany
    {
        return $this->hasMany(ProjectMilestone::class);
    }

    public function checklists(): HasMany
    {
        return $this->hasMany(ProjectChecklist::class);
    }

    public function surveys(): HasMany
    {
        return $this->hasMany(ProjectSurvey::class);
    }

    public function boqVersions(): HasMany
    {
        return $this->hasMany(BOQVersion::class);
    }

    public function boqApprovals(): HasMany
    {
        return $this->hasMany(BOQApproval::class);
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

    // New costing workflow methods
    public function isFullyApproved(): bool
    {
        // Check if all required approvals are in place based on costing type and amount
        $requiresMD = $this->requiresMDApproval();
        
        if ($requiresMD) {
            return $this->engineering_manager_approved && 
                   $this->finance_manager_approved && 
                   $this->md_approved;
        }
        
        return $this->engineering_manager_approved && $this->finance_manager_approved;
    }

    public function requiresMDApproval(): bool
    {
        // MD approval required for amounts above $10,000 or complex services
        return $this->total_budget > 10000 || 
               ($this->costing_type === 'Service Sales' && $this->isComplexService());
    }

    public function isComplexService(): bool
    {
        // Define complex service criteria (can be customized)
        $complexKeywords = ['EPC', 'Installation', 'Engineering'];
        return in_array($this->project_type, $complexKeywords);
    }

    public function getMinimumQuotesRequired(): int
    {
        return $this->emergency_procurement ? 1 : 3;
    }

    public function hasMinimumQuotes(): bool
    {
        $validQuotes = $this->quotes()->valid()->count();
        return $validQuotes >= $this->getMinimumQuotesRequired();
    }

    public function getSelectedQuote(): ?ProjectQuote
    {
        return $this->quotes()->selected()->first();
    }

    public function calculateVariance(): void
    {
        $variance = $this->actual_cost - $this->total_budget;
        $variancePercent = $this->total_budget > 0 ? ($variance / $this->total_budget) * 100 : 0;
        
        $this->update([
            'budget_variance' => $variance,
            'budget_variance_percent' => round($variancePercent, 2)
        ]);
    }

    public function hasSignificantVariance(): bool
    {
        return abs($this->budget_variance_percent) > 10; // 10% threshold
    }

    public function canCloseOut(): bool
    {
        return $this->status === 'Completed' && 
               $this->isFullyApproved() && 
               !$this->is_closed_out;
    }

    public function submitForApproval(string $approvalType, int $approverId, string $approverRole, ?array $data = null): ProjectApproval
    {
        return $this->approvals()->create([
            'approval_type' => $approvalType,
            'approver_role' => $approverRole,
            'approver_id' => $approverId,
            'status' => 'Pending',
            'submitted_at' => now(),
            'approval_data' => $data
        ]);
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
