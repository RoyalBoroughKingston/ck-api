<?php

namespace App\Models\Relationships;

use App\Models\Audit;
use App\Models\Organisation;
use App\Models\Role;
use App\Models\Service;
use App\Models\StatusUpdate;
use App\Models\UpdateRequest;
use App\Models\User;
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
        return $this->belongsToMany(Role::class, (new UserRole())->getTable())->distinct();
    }

    /**
     * This returns a collection of the roles assigned to the user
     * ordered by the highest role first.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function orderedRoles()
    {
        $sql = (new User())->getHighestRoleOrderSql();

        return $this->roles()->orderByRaw($sql['sql'], $sql['bindings']);
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
    public function statusUpdated()
    {
        return $this->hasMany(StatusUpdate::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function organisations()
    {
        return $this->belongsToMany(Organisation::class, table(UserRole::class))
            ->distinct();
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function services()
    {
        return $this->belongsToMany(Service::class, table(UserRole::class))
            ->distinct();
    }
}
