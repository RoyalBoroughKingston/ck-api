<?php

namespace App\Models\Scopes;

use App\Models\Role;
use App\Models\UserRole;
use Illuminate\Database\Eloquent\Builder;

trait UserScopes
{
    /**
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeGlobalAdmins(Builder $query): Builder
    {
        return $query->whereHas('userRoles', function (Builder $query) {
            return $query->where(table(UserRole::class, 'role_id'), Role::globalAdmin()->id);
        });
    }
}
