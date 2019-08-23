<?php

namespace App\Rules;

use App\Models\Organisation;
use App\Models\Role;
use App\Models\Service;
use App\Models\User;
use Illuminate\Contracts\Validation\Rule;

class CanRevokeRoleFromUser implements Rule
{
    /**
     * @var \App\Models\User
     */
    protected $user;

    /**
     * @var \App\Models\User
     */
    protected $subject;

    /**
     * @var array|null
     */
    protected $revokedRoles;

    /**
     * CanAssignRoleToUser constructor.
     *
     * @param \App\Models\User $user
     * @param \App\Models\User $subject
     * @param array|null $revokedRoles
     */
    public function __construct(User $user, User $subject, array $revokedRoles = null)
    {
        $this->user = $user;
        $this->subject = $subject;
        $this->revokedRoles = $revokedRoles;
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

        // Skip if the role is not provided in the revoked roles array.
        if ($this->shouldSkip($role)) {
            return true;
        }

        switch ($role['role']) {
            case Role::NAME_SERVICE_WORKER:
                $service = Service::query()->findOrFail($role['service_id']);
                if (!$this->user->canRevokeServiceWorker($this->subject, $service)) {
                    return false;
                }
                break;
            case Role::NAME_SERVICE_ADMIN:
                $service = Service::query()->findOrFail($role['service_id']);
                if (!$this->user->canRevokeServiceAdmin($this->subject, $service)) {
                    return false;
                }
                break;
            case Role::NAME_ORGANISATION_ADMIN:
                $organisation = Organisation::query()->findOrFail($role['organisation_id']);
                if (!$this->user->canRevokeOrganisationAdmin($this->subject, $organisation)) {
                    return false;
                }
                break;
            case Role::NAME_GLOBAL_ADMIN:
                if (!$this->user->canRevokeGlobalAdmin($this->subject)) {
                    return false;
                }
                break;
            case Role::NAME_SUPER_ADMIN:
                if (!$this->user->canRevokeSuperAdmin($this->subject)) {
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
        return 'You are unauthorised to revoke these roles for this user.';
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
        // If no revoked roles where provided then don't skip.
        if ($this->revokedRoles === null) {
            return false;
        }

        $revokedRoles = $this->parseRoles($this->revokedRoles);
        $role = $this->parseRoles($role);

        // If revoked role provided, and the role is in the array, then don't skip.
        foreach ($revokedRoles as $revokedRole) {
            if ($revokedRole == $role) {
                return false;
            }
        }

        // If revoked roles provided, but the role is not in the array, then skip.
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
