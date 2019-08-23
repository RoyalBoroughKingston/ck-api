<?php

namespace App\Rules;

use App\Models\Organisation;
use App\Models\Role;
use App\Models\Service;
use App\Models\User;
use Illuminate\Contracts\Validation\Rule;

class CanAssignRoleToUser implements Rule
{
    /**
     * @var \App\Models\User
     */
    protected $user;

    /**
     * @var array|null
     */
    protected $newRoles;

    /**
     * CanAssignRoleToUser constructor.
     *
     * @param \App\Models\User $user
     * @param array|null $newRoles
     */
    public function __construct(User $user, array $newRoles = null)
    {
        $this->user = $user;
        $this->newRoles = $newRoles;
    }

    /**
     * Determine if the validation rule passes.
     *
     * @param string $attribute
     * @param mixed $role
     * @return bool
     */
    public function passes($attribute, $role)
    {
        // Immediately fail if the value is not an array.
        if (!$this->validate($role)) {
            return false;
        }

        // Skip if the role is not provided in the new roles array.
        if ($this->shouldSkip($role)) {
            return true;
        }

        switch ($role['role']) {
            case Role::NAME_SERVICE_WORKER:
                $service = Service::findOrFail($role['service_id']);
                if (!$this->user->canMakeServiceWorker($service)) {
                    return false;
                }
                break;
            case Role::NAME_SERVICE_ADMIN:
                $service = Service::findOrFail($role['service_id']);
                if (!$this->user->canMakeServiceAdmin($service)) {
                    return false;
                }
                break;
            case Role::NAME_ORGANISATION_ADMIN:
                $organisation = Organisation::findOrFail($role['organisation_id']);
                if (!$this->user->canMakeOrganisationAdmin($organisation)) {
                    return false;
                }
                break;
            case Role::NAME_GLOBAL_ADMIN:
                if (!$this->user->canMakeGlobalAdmin()) {
                    return false;
                }
                break;
            case Role::NAME_SUPER_ADMIN:
                if (!$this->user->canMakeSuperAdmin()) {
                    return false;
                }
                break;
        }

        return true;
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return 'You are unauthorised to assign these roles to this user.';
    }

    /**
     * Validates the value.
     *
     * @param $role
     * @return bool
     */
    protected function validate($role): bool
    {
        // check if array.
        if (!is_array($role)) {
            return false;
        }

        // check if role key provided.
        if (!isset($role['role'])) {
            return false;
        }

        // Check if service_id or organisation_id provided (for certain roles).
        switch ($role['role']) {
            case Role::NAME_SERVICE_WORKER:
            case Role::NAME_SERVICE_ADMIN:
                if (!isset($role['service_id']) || !is_string($role['service_id'])) {
                    return false;
                }
                break;
            case Role::NAME_ORGANISATION_ADMIN:
                if (!isset($role['organisation_id']) || !is_string($role['organisation_id'])) {
                    return false;
                }
                break;
        }

        return true;
    }

    /**
     * @param array $role
     * @return bool
     */
    protected function shouldSkip(array $role): bool
    {
        // If no new roles where provided then don't skip.
        if ($this->newRoles === null) {
            return false;
        }

        $newRoles = $this->parseRoles($this->newRoles);
        $role = $this->parseRoles($role);

        // If new role provided, and the role is in the array, then don't skip.
        foreach ($newRoles as $newRole) {
            if ($newRole == $role) {
                return false;
            }
        }

        // If new roles provided, but the role is not in the array, then skip.
        return true;
    }

    /**
     * @param array $roles
     * @return array
     */
    protected function parseRoles(array $roles): array
    {
        $rolesCopy = isset($roles['role']) ? [$roles] : $roles;

        foreach ($rolesCopy as &$role) {
            switch ($role['role']) {
                case Role::NAME_ORGANISATION_ADMIN:
                    unset($role['service_id']);
                    break;
                case Role::NAME_GLOBAL_ADMIN:
                case Role::NAME_SUPER_ADMIN:
                    unset($role['service_id'], $role['organisation_id']);

                    break;
            }
        }

        return isset($roles['role']) ? $rolesCopy[0] : $rolesCopy;
    }
}
