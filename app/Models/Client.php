<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Client extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'email',
        'phone',
        'address',
        'company_name',
        'contact_person',
        'industry',
        'status',
        'notes'
    ];

    protected $casts = [
        'status' => 'string'
    ];

    // Relationships
    public function projects(): HasMany
    {
        return $this->hasMany(Project::class);
    }

    // Accessors
    public function getFullNameAttribute(): string
    {
        if ($this->company_name && $this->contact_person) {
            return "{$this->contact_person} - {$this->company_name}";
        }
        return $this->name ?: $this->company_name;
    }

    public function getActiveProjectsCountAttribute(): int
    {
        return $this->projects()->active()->count();
    }

    public function getTotalProjectValueAttribute(): float
    {
        return $this->projects()->sum('total_budget');
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function scopeByIndustry($query, $industry)
    {
        return $query->where('industry', $industry);
    }

    public function scopeSearch($query, $search)
    {
        return $query->where(function ($q) use ($search) {
            $q->where('name', 'like', "%{$search}%")
              ->orWhere('company_name', 'like', "%{$search}%")
              ->orWhere('email', 'like', "%{$search}%")
              ->orWhere('contact_person', 'like', "%{$search}%");
        });
    }
}
