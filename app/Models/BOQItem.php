<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BOQItem extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'boq_items';

    protected $fillable = [
        'boq_section_id',
        'project_id',
        'item_code',
        'description',
        'unit',
        'quantity',
        'rate',
        'total_amount',
        'category',
        'status',
        'notes',
        'metadata'
    ];

    protected $casts = [
        'quantity' => 'decimal:3',
        'rate' => 'decimal:2',
        'total_amount' => 'decimal:2',
        'metadata' => 'array'
    ];

    // Relationships
    public function section(): BelongsTo
    {
        return $this->belongsTo(BOQSection::class, 'boq_section_id');
    }

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    // Accessors
    public function getFormattedQuantityAttribute(): string
    {
        return number_format($this->quantity, 3) . ' ' . $this->unit;
    }

    public function getFormattedRateAttribute(): string
    {
        return '$' . number_format($this->rate, 2) . '/' . $this->unit;
    }

    public function getFormattedTotalAttribute(): string
    {
        return '$' . number_format($this->total_amount, 2);
    }

    public function getVarianceAttribute(): float
    {
        $calculatedTotal = $this->quantity * $this->rate;
        return $this->total_amount - $calculatedTotal;
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

    public function scopeApproved($query)
    {
        return $query->where('status', 'Approved');
    }

    // Methods
    public function calculateTotal(): void
    {
        $this->total_amount = $this->quantity * $this->rate;
        $this->save();
    }

    public function approve(): void
    {
        $this->status = 'Approved';
        $this->save();
        
        // Update section total
        $this->section->updateTotalAmount();
    }

    public function revise(): void
    {
        $this->status = 'Revised';
        $this->save();
    }

    protected static function boot()
    {
        parent::boot();

        static::saving(function ($item) {
            // Auto-calculate total amount
            $item->total_amount = $item->quantity * $item->rate;
        });

        static::saved(function ($item) {
            // Update section total when item changes
            $item->section->updateTotalAmount();
        });

        static::deleted(function ($item) {
            // Update section total when item is deleted
            $item->section->updateTotalAmount();
        });
    }
}