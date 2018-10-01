<?php

namespace App\Models\Relationships;

use App\Models\User;
use App\Models\UserRole;

trait RoleRelationships
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
    public function users()
    {
        return $this->belongsToMany(User::class, (new UserRole())->getTable())->withTrashed();
    }
}
