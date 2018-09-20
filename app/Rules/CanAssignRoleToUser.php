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
     * CanAssignRoleToUser constructor.
     *
     * @param \App\Models\User $user
     */
    public function __construct(User $user)
    {
        $this->user = $user;
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
        // Immediately fail if the value is not an array.
        if (!$this->validate($value)) {
            return false;
        }

        switch ($value['role']) {
            case Role::NAME_SERVICE_WORKER:
                $service = Service::findOrFail($value['service_id']);
                if (!$this->user->canMakeServiceWorker($service)) {
                    return false;
                }
                break;
            case Role::NAME_SERVICE_ADMIN:
                $service = Service::findOrFail($value['service_id']);
                if (!$this->user->canMakeServiceAdmin($service)) {
                    return false;
                }
                break;
            case Role::NAME_ORGANISATION_ADMIN:
                $organisation = Organisation::findOrFail($value['organisation_id']);
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
        return 'The :attribute field is invalid.';
    }

    /**
     * Validates the value.
     *
     * @param $value
     * @return bool
     */
    protected function validate($value): bool
    {
        // check if array.
        if (!is_array($value)) {
            return false;
        }

        // check if role key provided.
        if (!isset($value['role'])) {
            return false;
        }

        // Check if service_id or organisation_id provided (for certain roles).
        switch ($value['role']) {
            case Role::NAME_SERVICE_WORKER:
            case Role::NAME_SERVICE_ADMIN:
                if (!isset($value['service_id']) || !is_string($value['service_id'])) {
                    return false;
                }
                break;
            case Role::NAME_ORGANISATION_ADMIN:
                if (!isset($value['organisation_id']) || !is_string($value['organisation_id'])) {
                    return false;
                }
                break;
        }

        return true;
    }
}
