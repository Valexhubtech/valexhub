<?php

namespace Wave;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DemoRequest extends Model
{
    protected $fillable = [
        'name',
        'email',
        'phone',
        'referral_code',
        'product_id',
        'request_type',
        'status',
    ];

    protected $casts = [
        'request_type' => 'string',
        'status' => 'string',
    ];

    /**
     * Get the product that this demo request is for
     */
    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    /**
     * Generate WhatsApp URL with pre-filled message
     */
    public function generateWhatsAppUrl(): string
    {
        $phoneNumber = '2347082938319'; // Nigerian number

        if ($this->request_type === 'demo') {
            $message = "Hi, my name is {$this->name}, and I'd like to book a demo for {$this->product->name}. You can reach me at {$this->email}.";
        } else {
            $message = "Hi, my name is {$this->name}, and I want a quote for {$this->product->name}. You can reach me at {$this->email}.";
        }

        if ($this->referral_code) {
            $message .= " Referral code: {$this->referral_code}";
        }

        return "https://wa.me/{$phoneNumber}?text=".urlencode($message);
    }

    /**
     * Scope for pending requests
     */
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    /**
     * Scope for quote requests
     */
    public function scopeQuotes($query)
    {
        return $query->where('request_type', 'quote');
    }

    /**
     * Scope for demo requests
     */
    public function scopeDemos($query)
    {
        return $query->where('request_type', 'demo');
    }
}
