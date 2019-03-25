<?php

namespace App\Models\Relationships;

use App\Models\User;
use Laravel\Passport\Client;

trait AuditRelationships
{
    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user()
    {
        return $this->belongsTo(User::class)->withTrashed();
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function oauthClient()
    {
        return $this->belongsTo(Client::class);
    }
}
