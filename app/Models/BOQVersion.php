<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BOQVersion extends Model
{
    use HasFactory;

    protected $table = 'boq_versions';

    protected $fillable = [
        'project_id',
        'version_number',
        'version_name',
        'description',
        'status',
        'is_current',
        'snapshot_data',
        'total_amount',
        'sections_count',
        'items_count',
        'created_by',
        'approved_at',
        'approved_by',
    ];

    protected $casts = [
        'snapshot_data' => 'array',
        'total_amount' => 'decimal:2',
        'is_current' => 'boolean',
        'approved_at' => 'datetime',
    ];

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(Employee::class, 'created_by');
    }

    public function approver(): BelongsTo
    {
        return $this->belongsTo(Employee::class, 'approved_by');
    }

    public function approvals()
    {
        return $this->hasMany(BOQApproval::class);
    }

    // Set this version as current and unset others
    public function makeCurrent(): void
    {
        // Unset all other versions as current for this project
        static::where('project_id', $this->project_id)
            ->where('id', '!=', $this->id)
            ->update(['is_current' => false]);
        
        // Set this version as current
        $this->update(['is_current' => true]);
    }

    // Create snapshot of current BOQ state
    public function createSnapshot(): void
    {
        $project = $this->project;
        $sections = $project->boqSections()->with('boqItems')->get();
        
        $snapshot = [
            'sections' => $sections->toArray(),
            'created_at' => now()->toISOString(),
            'total_amount' => $sections->sum('total_amount'),
        ];
        
        $this->update([
            'snapshot_data' => $snapshot,
            'total_amount' => $snapshot['total_amount'],
            'sections_count' => $sections->count(),
            'items_count' => $sections->sum(fn($section) => $section->boqItems->count()),
        ]);
    }

    // Restore BOQ to this version's state
    public function restore(): bool
    {
        if (!$this->snapshot_data) {
            return false;
        }

        $project = $this->project;
        
        // Clear current BOQ data
        $project->boqSections()->delete();
        
        // Restore from snapshot
        foreach ($this->snapshot_data['sections'] as $sectionData) {
            $section = $project->boqSections()->create([
                'section_name' => $sectionData['section_name'],
                'section_code' => $sectionData['section_code'],
                'description' => $sectionData['description'],
                'status' => $sectionData['status'],
                'display_order' => $sectionData['display_order'],
            ]);
            
            foreach ($sectionData['boq_items'] as $itemData) {
                $section->boqItems()->create([
                    'item_code' => $itemData['item_code'],
                    'description' => $itemData['description'],
                    'unit' => $itemData['unit'],
                    'quantity' => $itemData['quantity'],
                    'rate' => $itemData['rate'],
                    'total_amount' => $itemData['total_amount'],
                    'status' => $itemData['status'],
                    'notes' => $itemData['notes'],
                ]);
            }
        }
        
        $this->makeCurrent();
        return true;
    }

    public function getFormattedVersionAttribute(): string
    {
        return $this->version_name 
            ? "{$this->version_number} ({$this->version_name})"
            : $this->version_number;
    }
}