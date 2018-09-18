<?php

namespace App\Notifications;

use Illuminate\Database\Eloquent\Relations\MorphMany;

interface Notifiable
{
    /**
     * @return \Illuminate\Database\Eloquent\Relations\MorphMany
     */
    public function notifications(): MorphMany;
}
