<?php

namespace Wave;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserProduct extends Model
{
    protected $guarded = [];

    protected $casts = [
        'amount_paid' => 'decimal:2',
        'purchase_date' => 'datetime',
        'next_renewal_date' => 'datetime',
    ];

    /**
     * Get the user that owns the user product
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(config('wave.user_model', User::class));
    }

    /**
     * Get the product that owns the user product
     */
    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    /**
     * Get the subscription associated with this user product
     */
    public function subscription(): BelongsTo
    {
        return $this->belongsTo(Subscription::class);
    }

    /**
     * Scope for active user products
     */
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    /**
     * Scope for expired user products
     */
    public function scopeExpired($query)
    {
        return $query->where('status', 'expired');
    }

    /**
     * Scope for cancelled user products
     */
    public function scopeCancelled($query)
    {
        return $query->where('status', 'cancelled');
    }

    /**
     * Check if the user product is active
     */
    public function isActive(): bool
    {
        return $this->status === 'active';
    }

    /**
     * Check if the user product is expired
     */
    public function isExpired(): bool
    {
        return $this->status === 'expired';
    }

    /**
     * Check if the user product is cancelled
     */
    public function isCancelled(): bool
    {
        return $this->status === 'cancelled';
    }

    /**
     * Check if the user product has a renewal date
     */
    public function hasRenewal(): bool
    {
        return !is_null($this->next_renewal_date);
    }

    /**
     * Check if the user product is due for renewal
     */
    public function isDueForRenewal(): bool
    {
        if (!$this->hasRenewal()) {
            return false;
        }

        return $this->next_renewal_date <= now();
    }

    /**
     * Mark the user product as active
     */
    public function markAsActive(): bool
    {
        $this->status = 'active';
        return $this->save();
    }

    /**
     * Mark the user product as expired
     */
    public function markAsExpired(): bool
    {
        $this->status = 'expired';
        return $this->save();
    }

    /**
     * Mark the user product as cancelled
     */
    public function markAsCancelled(): bool
    {
        $this->status = 'cancelled';
        return $this->save();
    }

    /**
     * Mark the user product as inactive
     */
    public function markAsInactive(): bool
    {
        $this->status = 'inactive';
        return $this->save();
    }

    /**
     * Extend the renewal date
     */
    public function extendRenewal($period = 'month', $count = 1): bool
    {
        $currentDate = $this->next_renewal_date ?? now();
        
        switch ($period) {
            case 'month':
                $this->next_renewal_date = $currentDate->addMonths($count);
                break;
            case 'year':
                $this->next_renewal_date = $currentDate->addYears($count);
                break;
            case 'day':
                $this->next_renewal_date = $currentDate->addDays($count);
                break;
            default:
                return false;
        }

        return $this->save();
    }

    /**
     * Get formatted amount paid
     */
    public function getFormattedAmountAttribute(): string
    {
        return '$' . number_format($this->amount_paid, 2);
    }

    /**
     * Get days until renewal
     */
    public function getDaysUntilRenewalAttribute(): ?int
    {
        if (!$this->hasRenewal()) {
            return null;
        }

        return now()->diffInDays($this->next_renewal_date, false);
    }
}