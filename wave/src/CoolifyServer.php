<?php

namespace Wave;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CoolifyServer extends Model
{
    protected $guarded = [];

    protected $casts = [
        'api_token'       => 'encrypted',
        'max_deployments' => 'integer',
        'monthly_cost'    => 'decimal:2',
        'sort_order'      => 'integer',
    ];

    public function deployments(): HasMany
    {
        return $this->hasMany(Deployment::class);
    }

    /** Active (non-terminated, non-failed) deployments consuming capacity. */
    public function activeDeployments(): HasMany
    {
        return $this->hasMany(Deployment::class)
            ->whereNotIn('status', ['terminated', 'failed']);
    }

    public function isFull(): bool
    {
        return $this->activeDeployments()->count() >= $this->max_deployments;
    }

    public function getUsedSlotsAttribute(): int
    {
        return $this->activeDeployments()->count();
    }

    public function getAvailableSlotsAttribute(): int
    {
        return max(0, $this->max_deployments - $this->used_slots);
    }

    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }
}
