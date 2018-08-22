<?php

namespace App\Models;

use App\Models\Mutators\NotificationMutators;
use App\Models\Relationships\NotificationRelationships;
use App\Models\Scopes\NotificationScopes;

class Notification extends Model
{
    use NotificationMutators;
    use NotificationRelationships;
    use NotificationScopes;

    const CHANNEL_EMAIL = 'email';
    const CHANNEL_SMS = 'sms';

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'sent_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];
}
