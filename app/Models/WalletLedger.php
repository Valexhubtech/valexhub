<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WalletLedger extends Model
{
    protected $fillable = [
        'type',
        'amount',
        'currency',
        'note',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
    ];

    public static function balance(string $currency = 'NGN'): float
    {
        $topUps    = self::where('type', 'top_up')->where('currency', $currency)->sum('amount');
        $purchases = self::where('type', 'purchase')->where('currency', $currency)->sum('amount');

        return (float) $topUps - (float) $purchases;
    }

    public static function recordPurchase(float $amount, string $note, string $currency = 'NGN'): self
    {
        return self::create(['type' => 'purchase', 'amount' => $amount, 'currency' => $currency, 'note' => $note]);
    }

    public static function recordTopUp(float $amount, string $note, string $currency = 'NGN'): self
    {
        return self::create(['type' => 'top_up', 'amount' => $amount, 'currency' => $currency, 'note' => $note]);
    }
}
