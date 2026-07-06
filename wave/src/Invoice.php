<?php

namespace Wave;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Invoice extends Model
{
    protected $guarded = [];

    protected $casts = [
        'line_items' => 'array',
        'amount'     => 'decimal:2',
        'due_date'   => 'datetime',
        'paid_at'    => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(config('wave.user_model', User::class));
    }

    public function userProduct(): BelongsTo
    {
        return $this->belongsTo(UserProduct::class);
    }

    public function deployment(): BelongsTo
    {
        return $this->belongsTo(Deployment::class);
    }

    public function isPaid(): bool
    {
        return $this->status === 'paid';
    }

    public function formattedAmount(): string
    {
        return '₦' . number_format((float) $this->amount, 2);
    }

    public function invoiceNumber(): string
    {
        return 'INV-' . str_pad($this->id, 6, '0', STR_PAD_LEFT);
    }
}
