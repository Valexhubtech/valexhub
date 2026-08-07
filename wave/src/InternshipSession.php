<?php

namespace Wave;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class InternshipSession extends Model
{
    protected $guarded = [];

    protected $casts = [
        'roles'                => 'array',
        'is_active'            => 'boolean',
        'application_deadline' => 'date',
    ];

    protected static function boot(): void
    {
        parent::boot();

        static::creating(function (self $session) {
            if (empty($session->slug)) {
                $session->slug = Str::slug($session->name);
            }
        });
    }

    public function applications(): HasMany
    {
        return $this->hasMany(InternshipApplication::class);
    }

    public function isOpen(): bool
    {
        if (! $this->is_active) {
            return false;
        }

        return $this->application_deadline === null || $this->application_deadline->isFuture();
    }

    public static function getActive(): ?self
    {
        return self::where('is_active', true)->latest()->first();
    }
}
