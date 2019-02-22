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
     * @param \App\Models\Service|null $service
     * @return bool
     */
    public function isServiceWorker(Service $service = null): bool
    {
        $isServiceAdmin = $this->role->name === Role::NAME_SERVICE_WORKER;

        return $service
            ? ($isServiceAdmin && $this->service_id === $service->id)
            : $isServiceAdmin;
    }

    /**
     * @param \App\Models\Service|null $service
     * @return bool
     */
    public function isServiceAdmin(Service $service = null): bool
    {
        $isServiceAdmin = $this->role->name === Role::NAME_SERVICE_ADMIN;

        return $service
            ? ($isServiceAdmin && $this->service_id === $service->id)
            : $isServiceAdmin;
    }

    /**
     * @param \App\Models\Organisation|null $organisation
     * @return bool
     */
    public function isOrganisationAdmin(Organisation $organisation = null): bool
    {
        $isOrganisationAdmin = $this->role->name === Role::NAME_ORGANISATION_ADMIN;

        return $organisation
            ? ($isOrganisationAdmin && $this->organisation_id === $organisation->id)
            : $isOrganisationAdmin;
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
