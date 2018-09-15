<?php

namespace App\Models;

use App\Models\Mutators\UserRoleMutators;
use App\Models\Relationships\UserRoleRelationships;
use App\Models\Scopes\UserRoleScopes;

class UserRole extends Model
{
    use UserRoleMutators;
    use UserRoleRelationships;
    use UserRoleScopes;

    /**
     * @return bool
     */
    public function isServiceWorker(): bool
    {
        return $this->role->name === Role::NAME_SERVICE_WORKER;
    }

    /**
     * @return bool
     */
    public function isServiceAdmin(): bool
    {
        return $this->role->name === Role::NAME_SERVICE_ADMIN;
    }

    /**
     * @return bool
     */
    public function isOrganisationAdmin(): bool
    {
        return $this->role->name === Role::NAME_ORGANISATION_ADMIN;
    }

    /**
     * @return bool
     */
    public function isGlobalAdmin(): bool
    {
        return $this->role->name === Role::NAME_GLOBAL_ADMIN;
    }

    /**
     * @return bool
     */
    public function isSuperAdmin(): bool
    {
        return $this->role->name === Role::NAME_SUPER_ADMIN;
    }
}
