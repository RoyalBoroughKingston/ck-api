<?php

namespace App\Models\Relationships;

trait NotificationRelationships
{
    /**
     * @return \Illuminate\Database\Eloquent\Relations\MorphTo
     */
    public function notifiable()
    {
        return $this->morphTo('notifiable');
    }
}
