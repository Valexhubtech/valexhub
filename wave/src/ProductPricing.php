<?php

namespace Wave;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProductPricing extends Model
{
    protected $table = 'product_pricing';

    protected $guarded = [];

    protected $casts = [
        'amount' => 'decimal:2',
        'is_required' => 'boolean',
        'is_active' => 'boolean',
    ];

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function isRecurring(): bool
    {
        return in_array($this->price_type, ['monthly', 'quarterly', 'yearly']);
    }

    public function getLabelAttribute(): string
    {
        return match ($this->price_type) {
            'monthly' => 'Monthly',
            'quarterly' => 'Quarterly',
            'yearly' => 'Yearly',
            'setup' => 'Setup Fee',
            'license' => 'License Fee',
            'onetime' => 'One-time',
            default => ucfirst($this->price_type),
        };
    }
}
