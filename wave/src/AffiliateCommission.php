<?php

namespace Wave;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AffiliateCommission extends Model
{
    protected $guarded = [];

    protected $casts = [
        'plan_monthly_price' => 'decimal:2',
        'commission_rate' => 'decimal:2',
        'commission_amount' => 'decimal:2',
        'accrued_at' => 'datetime',
        'claimed_at' => 'datetime',
    ];

    /**
     * Get the affiliate who earns this commission
     */
    public function affiliate(): BelongsTo
    {
        return $this->belongsTo(config('wave.user_model', User::class), 'affiliate_id');
    }

    /**
     * Get the referred user whose payment generated this commission
     */
    public function referredUser(): BelongsTo
    {
        return $this->belongsTo(config('wave.user_model', User::class), 'referred_user_id');
    }

    /**
     * Get the subscription this commission was earned from
     */
    public function subscription(): BelongsTo
    {
        return $this->belongsTo(Subscription::class);
    }

    /**
     * Get the payment transaction that triggered this commission
     */
    public function paymentTransaction(): BelongsTo
    {
        return $this->belongsTo(PaymentTransaction::class);
    }

    /**
     * Scope for accrued (claimable) commissions
     */
    public function scopeAccrued($query)
    {
        return $query->where('status', 'accrued');
    }
}
