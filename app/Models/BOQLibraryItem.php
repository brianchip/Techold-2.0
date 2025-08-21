<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Builder;

class BOQLibraryItem extends Model
{
    use HasFactory;

    protected $table = 'boq_library_items';

    protected $fillable = [
        'item_code',
        'item_name',
        'description',
        'category',
        'unit',
        'standard_rate',
        'min_rate',
        'max_rate',
        'supplier',
        'specifications',
        'custom_fields',
        'is_active',
        'is_template',
        'usage_count',
        'last_updated_price',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'custom_fields' => 'array',
        'standard_rate' => 'decimal:2',
        'min_rate' => 'decimal:2',
        'max_rate' => 'decimal:2',
        'is_active' => 'boolean',
        'is_template' => 'boolean',
        'last_updated_price' => 'date',
    ];

    public function creator(): BelongsTo
    {
        return $this->belongsTo(Employee::class, 'created_by');
    }

    public function updater(): BelongsTo
    {
        return $this->belongsTo(Employee::class, 'updated_by');
    }

    // Scopes
    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    public function scopeByCategory(Builder $query, string $category): Builder
    {
        return $query->where('category', $category);
    }

    public function scopeTemplates(Builder $query): Builder
    {
        return $query->where('is_template', true);
    }

    public function scopePopular(Builder $query): Builder
    {
        return $query->orderBy('usage_count', 'desc');
    }

    public function scopeSearch(Builder $query, string $search): Builder
    {
        return $query->where(function ($q) use ($search) {
            $q->where('item_name', 'like', "%{$search}%")
              ->orWhere('description', 'like', "%{$search}%")
              ->orWhere('item_code', 'like', "%{$search}%");
        });
    }

    // Methods
    public function incrementUsage(): void
    {
        $this->increment('usage_count');
    }

    public function updatePrice(float $newRate, int $updatedBy): void
    {
        $this->update([
            'standard_rate' => $newRate,
            'last_updated_price' => now(),
            'updated_by' => $updatedBy,
        ]);
    }

    public function createBOQItem(int $boqSectionId, float $quantity, array $overrides = []): BOQItem
    {
        $this->incrementUsage();
        
        return BOQItem::create(array_merge([
            'boq_section_id' => $boqSectionId,
            'item_code' => $this->item_code,
            'description' => $this->description,
            'unit' => $this->unit,
            'quantity' => $quantity,
            'rate' => $this->standard_rate,
            'total_amount' => $quantity * $this->standard_rate,
            'status' => 'Draft',
        ], $overrides));
    }

    // Duplicate this library item
    public function duplicate(string $newItemCode, int $createdBy): self
    {
        return static::create([
            'item_code' => $newItemCode,
            'item_name' => $this->item_name . ' (Copy)',
            'description' => $this->description,
            'category' => $this->category,
            'unit' => $this->unit,
            'standard_rate' => $this->standard_rate,
            'min_rate' => $this->min_rate,
            'max_rate' => $this->max_rate,
            'supplier' => $this->supplier,
            'specifications' => $this->specifications,
            'custom_fields' => $this->custom_fields,
            'is_active' => true,
            'is_template' => $this->is_template,
            'created_by' => $createdBy,
        ]);
    }

    // Get category options
    public static function getCategoryOptions(): array
    {
        return [
            'Materials' => 'Materials',
            'Labor' => 'Labor', 
            'Equipment' => 'Equipment',
            'Subcontractor' => 'Subcontractor',
            'Overhead' => 'Overhead',
            'Other' => 'Other',
        ];
    }

    // Get rate range as formatted string
    public function getRateRangeAttribute(): string
    {
        if ($this->min_rate && $this->max_rate) {
            return "$" . number_format($this->min_rate, 2) . " - $" . number_format($this->max_rate, 2);
        }
        return "$" . number_format($this->standard_rate, 2);
    }

    // Get category color for UI
    public function getCategoryColorAttribute(): string
    {
        return match($this->category) {
            'Materials' => 'blue',
            'Labor' => 'green',
            'Equipment' => 'purple',
            'Subcontractor' => 'orange',
            'Overhead' => 'gray',
            default => 'indigo',
        };
    }

    // Check if price needs updating (older than 90 days)
    public function getPriceNeedsUpdateAttribute(): bool
    {
        return !$this->last_updated_price || 
               $this->last_updated_price->diffInDays(now()) > 90;
    }
}