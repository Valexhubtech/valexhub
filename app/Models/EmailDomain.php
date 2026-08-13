<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EmailDomain extends Model
{
    protected $fillable = [
        'instance_id',
        'domain',
        'is_shared',
        'stage',
        'status',
        'records',
        'plume_api_key',
        'stalled_at',
    ];

    protected $casts = [
        'is_shared'  => 'boolean',
        'records'    => 'array',
        'stalled_at' => 'datetime',
    ];

    protected $hidden = ['plume_api_key'];

    public function isActive(): bool
    {
        return $this->status === 'active';
    }

    public function isVerifying(): bool
    {
        return $this->status === 'verifying';
    }

    public function isManual(): bool
    {
        return $this->status === 'manual';
    }
}
