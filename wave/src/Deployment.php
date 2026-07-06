<?php

namespace Wave;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Deployment extends Model
{
    protected $guarded = [];

    protected $casts = [
        'credentials_encrypted' => 'encrypted:array',
        'deployed_at' => 'datetime',
    ];

    /**
     * Get the user that owns the deployment
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(config('wave.user_model', User::class));
    }

    /**
     * Get the product that was deployed
     */
    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    /**
     * Get the purchase record behind this deployment
     */
    public function userProduct(): BelongsTo
    {
        return $this->belongsTo(UserProduct::class);
    }

    public function coolifyServer(): BelongsTo
    {
        return $this->belongsTo(CoolifyServer::class);
    }

    public function domainPurchases(): HasMany
    {
        return $this->hasMany(DomainPurchase::class);
    }

    /**
     * Check if the deployment is active and ready to use
     */
    public function isActive(): bool
    {
        return $this->status === 'active';
    }
}
