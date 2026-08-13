<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Domain extends Model
{
    protected $fillable = [
        'domain',
        'owner',
        'registrar',
        'dns_host',
        'managed',
    ];

    protected $casts = [
        'managed' => 'boolean',
    ];

    public function dnsChanges(): HasMany
    {
        return $this->hasMany(DnsChange::class, 'domain', 'domain');
    }

    public function isOurs(): bool
    {
        return $this->owner === 'us';
    }

    public function isDesecManaged(): bool
    {
        return $this->dns_host === 'desec';
    }
}
