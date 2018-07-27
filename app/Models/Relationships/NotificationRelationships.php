<?php

namespace App\Models\Relationships;

use App\Models\User;

trait NotificationRelationships
{
    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
