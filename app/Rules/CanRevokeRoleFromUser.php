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
     * @param  string  $attribute
     * @param  mixed  $value
     * @return bool
     */
    public function passes($attribute, $value)
    {
        // Use the roles passed or fallback to the value.
        $roles = $this->revokedRoles ?? [$value];

        foreach ($roles as $role) {
            // Immediately fail if the value is not an array.
            if (!$this->validate($role)) {
                return false;
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
}
