<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Str;
use Spatie\Permission\Models\Role;
use Wave\ActivityLog;
use Wave\AffiliateCommission;
use Wave\Deployment;
use Wave\Invoice;
use Wave\PaymentTransaction;
use Wave\Traits\HasProfileKeyValues;
use Wave\User as WaveUser;
use Wave\UserProduct;

class User extends WaveUser
{
    use HasFactory, HasProfileKeyValues, Notifiable, SoftDeletes;

    public $guard_name = 'web';

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'notification_preferences' => 'array',
            'social_links' => 'array',
            'privacy_settings' => 'array',
            'deletion_scheduled_at' => 'datetime',
        ];
    }

    public function activityLogs(): HasMany
    {
        return $this->hasMany(ActivityLog::class);
    }

    /**
     * Get the user's products
     */
    public function userProducts(): HasMany
    {
        return $this->hasMany(UserProduct::class);
    }

    /**
     * Get the user's active products
     */
    public function activeProducts(): HasMany
    {
        return $this->userProducts()->where('status', 'active');
    }

    /**
     * Check if user owns a specific product
     */
    public function ownsProduct($productId): bool
    {
        return $this->userProducts()
            ->where('product_id', $productId)
            ->where('status', 'active')
            ->exists();
    }

    /**
     * Get the user who referred this user (their affiliate)
     */
    public function referrer(): BelongsTo
    {
        return $this->belongsTo(self::class, 'referrer_id');
    }

    /**
     * Get the users this user has referred (their clients)
     */
    public function referrals(): HasMany
    {
        return $this->hasMany(self::class, 'referrer_id');
    }

    /**
     * Check if this user is a sub-affiliate enroller, i.e. has referred
     * users who have themselves gone on to refer others.
     */
    public function hasSubAffiliates(): bool
    {
        return $this->referrals()->whereHas('referrals')->exists();
    }

    /**
     * Get the commissions this user has earned as an affiliate
     */
    public function affiliateCommissions(): HasMany
    {
        return $this->hasMany(AffiliateCommission::class, 'affiliate_id');
    }

    /**
     * Get the deployments this user owns
     */
    public function deployments(): HasMany
    {
        return $this->hasMany(Deployment::class);
    }

    /**
     * Get the payment transactions belonging to this user
     */
    public function paymentTransactions(): HasMany
    {
        return $this->hasMany(PaymentTransaction::class);
    }

    public function invoices(): HasMany
    {
        return $this->hasMany(Invoice::class);
    }

    protected static function boot()
    {
        parent::boot();

        // Listen for the creating event of the model
        static::creating(function ($user) {
            // Check if the username attribute is empty
            if (empty($user->username)) {
                // Use the name to generate a slugified username
                $username = Str::slug($user->name, '');
                $i = 1;
                while (self::where('username', $username)->exists()) {
                    $username = Str::slug($user->name, '').$i;
                    $i++;
                }
                $user->username = $username;
            }

            // Generate a unique referral code for this user's own referral link
            if (empty($user->referral_code)) {
                do {
                    $referralCode = Str::lower(Str::random(8));
                } while (self::where('referral_code', $referralCode)->exists());
                $user->referral_code = $referralCode;
            }

            // Capture the referring affiliate from the signed cookie set by ReferralController,
            // guarding against self-referral and unknown/forged codes.
            if (empty($user->referrer_id) && Cookie::has('referral_code')) {
                $referrer = self::where('referral_code', Cookie::get('referral_code'))->first();
                if ($referrer && $referrer->email !== $user->email) {
                    $user->referrer_id = $referrer->id;
                }
            }
        });

        // Listen for the created event of the model
        static::created(function ($user) {
            // Remove all roles
            $user->syncRoles([]);

            // Assign the default role if it exists
            $defaultRole = config('wave.default_user_role', 'registered');
            if (Role::where('name', $defaultRole)->where('guard_name', 'web')->exists()) {
                $user->assignRole($defaultRole);
            }
        });
    }
}
