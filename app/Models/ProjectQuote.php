<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProjectQuote extends Model
{
    use HasFactory;

    protected $fillable = [
        'project_id',
        'supplier_name',
        'supplier_contact',
        'quote_reference',
        'quote_amount',
        'currency',
        'quote_date',
        'valid_until',
        'items_description',
        'line_items',
        'status',
        'is_authorized_distributor',
        'is_emergency_quote',
        'notes',
        'quote_file_path'
    ];

    protected $casts = [
        'quote_date' => 'date',
        'valid_until' => 'date',
        'line_items' => 'array',
        'quote_amount' => 'decimal:2',
        'is_authorized_distributor' => 'boolean',
        'is_emergency_quote' => 'boolean'
    ];

    /**
     * Get the project that owns the quote
     */
    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    /**
     * Check if quote is still valid
     */
    public function isValid(): bool
    {
        return $this->valid_until === null || $this->valid_until >= now()->toDateString();
    }

    /**
     * Check if quote is expired
     */
    public function isExpired(): bool
    {
        return $this->valid_until !== null && $this->valid_until < now()->toDateString();
    }

    /**
     * Get formatted quote amount with currency
     */
    public function getFormattedAmountAttribute(): string
    {
        return $this->currency . ' ' . number_format($this->quote_amount, 2);
    }

    /**
     * Scope for valid quotes
     */
    public function scopeValid($query)
    {
        return $query->where(function($q) {
            $q->whereNull('valid_until')
              ->orWhere('valid_until', '>=', now()->toDateString());
        });
    }

    /**
     * Scope for pending quotes
     */
    public function scopePending($query)
    {
        return $query->where('status', 'Pending');
    }

    /**
     * Scope for selected quotes
     */
    public function scopeSelected($query)
    {
        return $query->where('status', 'Selected');
    }
}