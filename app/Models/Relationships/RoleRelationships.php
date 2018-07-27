<?php

namespace App\Models\Relationships;

use App\Models\User;
use App\Models\UserRole;

trait RoleRelationships
{
    /**
     * @return mixed
     */
    public function userRoles()
    {
        return $this->hasMany(UserRole::class);
    }

    public function users()
    {
        return $this->belongsToMany(User::class, (new UserRole())->getTable());
    }
}
