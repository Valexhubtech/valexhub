<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DomainOrder extends Model
{
    protected $fillable = [
        'instance_id',
        'domain',
        'price',
        'currency',
        'state',
        'go54_reference',
        'last_error',
    ];

    protected $casts = [
        'price' => 'decimal:2',
    ];

    public function isAwaitingWallet(): bool
    {
        return $this->state === 'awaiting_wallet';
    }

    public function isActive(): bool
    {
        return $this->state === 'active';
    }

    public function advanceTo(string $state, ?string $error = null): void
    {
        $this->state      = $state;
        $this->last_error = $error;
        $this->save();
    }
}
