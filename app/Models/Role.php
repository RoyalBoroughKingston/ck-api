<?php

namespace App\Models;

use App\Models\Mutators\RoleMutators;
use App\Models\Relationships\RoleRelationships;
use App\Models\Scopes\RoleScopes;

class Role extends Model
{
    use RoleMutators;
    use RoleRelationships;
    use RoleScopes;

    /**
     * @return \App\Models\Role
     */
    public static function serviceWorker(): self
    {
        return static::where('name', 'Service Worker')->firstOfFail();
    }

    /**
     * @return \App\Models\Role
     */
    public static function serviceAdmin(): self
    {
        return static::where('name', 'Service Admin')->firstOfFail();
    }

    /**
     * @return \App\Models\Role
     */
    public static function organisationAdmin(): self
    {
        return static::where('name', 'Organisation Admin')->firstOfFail();
    }

    /**
     * @return \App\Models\Role
     */
    public static function globalAdmin(): self
    {
        return static::where('name', 'Global Admin')->firstOfFail();
    }

    /**
     * @return \App\Models\Role
     */
    public static function superAdmin(): self
    {
        return static::where('name', 'Super Admin')->firstOfFail();
    }
}
