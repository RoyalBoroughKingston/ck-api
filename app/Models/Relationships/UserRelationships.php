<?php

namespace App\Models\Relationships;

use App\Models\Audit;
use App\Models\Notification;
use App\Models\Role;
use App\Models\StatusUpdate;
use App\Models\UpdateRequest;
use App\Models\UserRole;

trait UserRelationships
{
    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function userRoles()
    {
        return $this->hasMany(UserRole::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function roles()
    {
        return $this->belongsToMany(Role::class, (new UserRole())->getTable());
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function updateRequests()
    {
        return $this->hasMany(UpdateRequest::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function audits()
    {
        return $this->hasMany(Audit::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function notifications()
    {
        return $this->hasMany(Notification::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function statusUpdated()
    {
        return $this->hasMany(StatusUpdate::class);
    }
}
