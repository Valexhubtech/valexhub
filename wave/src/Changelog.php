<?php

namespace Wave;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class Changelog extends Model
{
    protected $fillable = ['title', 'description', 'body', 'loggable_type', 'loggable_id'];

    public function users(): BelongsToMany
    {
        return $this->belongsToMany('Wave\User');
    }

    /**
     * Get the owning loggable model (product, etc.)
     */
    public function loggable(): MorphTo
    {
        return $this->morphTo();
    }
}
