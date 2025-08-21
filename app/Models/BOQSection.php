<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class BOQSection extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'boq_sections';

    protected $fillable = [
        'project_id',
        'section_name',
        'section_code',
        'description',
        'display_order',
        'total_amount',
        'status'
    ];

    protected $casts = [
        'total_amount' => 'decimal:2',
        'display_order' => 'integer'
    ];

    // Relationships
    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(BOQItem::class, 'boq_section_id');
    }

    // Accessors
    public function getItemsCountAttribute(): int
    {
        return $this->items()->count();
    }

    public function getCompletionPercentageAttribute(): float
    {
        $totalItems = $this->items()->count();
        if ($totalItems === 0) return 0;
        
        $approvedItems = $this->items()->where('status', 'Approved')->count();
        return ($approvedItems / $totalItems) * 100;
    }

    public function getCalculatedTotalAttribute(): float
    {
        return $this->items()->sum('total_amount');
    }

    // Scopes
    public function scopeByStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    public function scopeOrdered($query)
    {
        return $query->orderBy('display_order');
    }

    // Methods
    public function updateTotalAmount(): void
    {
        $this->total_amount = $this->items()->sum('total_amount');
        $this->save();
    }

    public function recalculateItems(): void
    {
        $this->total_items = $this->items()->count();
        $this->updateTotalAmount();
    }

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($section) {
            if (!$section->section_code) {
                $section->section_code = 'SEC' . str_pad($section->project_id, 3, '0', STR_PAD_LEFT) . 
                                       str_pad(static::where('project_id', $section->project_id)->count() + 1, 3, '0', STR_PAD_LEFT);
            }
        });
    }
}