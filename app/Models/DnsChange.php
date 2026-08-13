<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DnsChange extends Model
{
    protected $fillable = [
        'domain',
        'actor',
        'action',
        'record_type',
        'subname',
        'before',
        'after',
    ];

    protected $casts = [
        'before' => 'array',
        'after'  => 'array',
    ];

    public static function log(
        string $domain,
        string $actor,
        string $action,
        string $type,
        string $subname,
        ?array $before = null,
        ?array $after = null,
    ): void {
        self::create([
            'domain'      => $domain,
            'actor'       => $actor,
            'action'      => $action,
            'record_type' => $type,
            'subname'     => $subname,
            'before'      => $before,
            'after'       => $after,
        ]);
    }
}
