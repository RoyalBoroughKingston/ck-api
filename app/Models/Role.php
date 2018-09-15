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

    const NAME_SERVICE_WORKER = 'Service Worker';
    const NAME_SERVICE_ADMIN = 'Service Admin';
    const NAME_ORGANISATION_ADMIN = 'Organisation Admin';
    const NAME_GLOBAL_ADMIN = 'Global Admin';
    const NAME_SUPER_ADMIN = 'Super Admin';

    /**
     * @return \App\Models\Role
     */
    public static function serviceWorker(): self
    {
        return cache()->rememberForever('Role::serviceWorker', function () {
            return static::query()->where('name', static::NAME_SERVICE_WORKER)->firstOrFail();
        });
    }

    /**
     * @return \App\Models\Role
     */
    public static function serviceAdmin(): self
    {
        return cache()->rememberForever('Role::serviceAdmin', function () {
            return static::query()->where('name', static::NAME_SERVICE_ADMIN)->firstOrFail();
        });
    }

    /**
     * @return \App\Models\Role
     */
    public static function organisationAdmin(): self
    {
        return cache()->rememberForever('Role::organisationAdmin', function () {
            return static::query()->where('name', static::NAME_ORGANISATION_ADMIN)->firstOrFail();
        });
    }

    /**
     * @return \App\Models\Role
     */
    public static function globalAdmin(): self
    {
        return cache()->rememberForever('Role::globalAdmin', function () {
            return static::query()->where('name', static::NAME_GLOBAL_ADMIN)->firstOrFail();
        });
    }

    /**
     * @return \App\Models\Role
     */
    public static function superAdmin(): self
    {
        return cache()->rememberForever('Role::superAdmin', function () {
            return static::query()->where('name', static::NAME_SUPER_ADMIN)->firstOrFail();
        });
    }
}
