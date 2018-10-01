<?php

namespace App\Models\Relationships;

use App\Models\User;

trait UpdateRequestRelationships
{
    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user()
    {
        return $this->belongsTo(User::class)->withTrashed();
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\MorphTo
     */
    public function updateable()
    {
        return $this->morphTo('updateable');
    }
}
