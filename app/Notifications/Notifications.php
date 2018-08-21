<?php

namespace App\Notifications;

use App\Models\Notification;

trait Notifications
{
    /**
     * @return \Illuminate\Database\Eloquent\Relations\MorphMany
     */
    public function notifications()
    {
        return $this->morphMany(Notification::class, 'notifiable');
    }
}
