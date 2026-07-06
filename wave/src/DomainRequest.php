<?php

namespace Wave;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DomainRequest extends Model
{
    protected $guarded = [];

    /**
     * Get the deployment this domain request belongs to
     */
    public function deployment(): BelongsTo
    {
        return $this->belongsTo(Deployment::class);
    }

    /**
     * Get the user who made the request
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(config('wave.user_model', User::class));
    }
}
