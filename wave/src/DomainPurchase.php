<?php

namespace Wave;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DomainPurchase extends Model
{
    protected $guarded = [];

    protected $casts = [
        'paid_at'         => 'datetime',
        'nameservers'     => 'array',
        'dns_records'     => 'array',
        'registrar_data'  => 'array',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function deployment(): BelongsTo
    {
        return $this->belongsTo(Deployment::class);
    }

    public function getDomainPriceNairaAttribute(): float
    {
        return $this->domain_price_kobo / 100;
    }

    public function getSetupFeeNairaAttribute(): float
    {
        return $this->setup_fee_kobo / 100;
    }

    public function getTotalNairaAttribute(): float
    {
        return $this->total_kobo / 100;
    }
}
