<?php

namespace Wave;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProductAddon extends Model
{
    protected $guarded = [];

    protected $casts = [
        'price' => 'decimal:2',
        'is_active' => 'boolean',
    ];

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function isRecurring(): bool
    {
        return $this->price_type === 'recurring';
    }
}
