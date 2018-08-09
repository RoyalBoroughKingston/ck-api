<?php

namespace App\Http\Requests\User;

use App\Models\Organisation;
use App\Models\Role;
use App\Models\Service;
use App\Models\UserRole;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        $canUpdate = $this->user()->canUpdate($this->route('user'));
        $canAddNewRoles = $this->canAddNewRoles($this->getNewRoles());
        $canRevokeDeletedRoles = $this->canRevokeDeletedRoles($this->getDeletedRoles());

        if ($canUpdate && $canAddNewRoles && $canRevokeDeletedRoles) {
            return true;
        }

        return false;
    }

    /**
     * @return array
     */
    protected function getExistingRoles(): array
    {
        /** @var \App\Models\User $user */
        $user = $this->route('user');

        $exitingRoles = $user->userRoles->load('role');

        $existingRolesArray = $exitingRoles->map(function (UserRole $userRole) {
            return array_filter_null([
                'role' => $userRole->role->name,
                'organisation_id' => $userRole->organisation_id,
                'service_id' => $userRole->service_id,
            ]);
        })->toArray();

        return $existingRolesArray;
    }

    /**
     * @return array
     */
    public function getNewRoles(): array
    {
        return array_diff_multi($this->roles, $this->getExistingRoles());
    }

    /**
     * @return array
     */
    public function getDeletedRoles(): array
    {
        return array_diff_multi($this->getExistingRoles(), $this->roles);
    }

    /**
     * @param array $newRoles
     * @return bool
     */
    protected function canAddNewRoles(array $newRoles): bool
    {
        /** @var \App\Models\User $user */
        $user = $this->route('user');

        foreach ($newRoles as $newRole) {
            $service = isset($newRole['service_id']) ? Service::findOrFail($newRole['service_id']) : null;
            $organisation = isset($newRole['organisation_id']) ? Organisation::findOrFail($newRole['organisation_id']) : null;

            switch ($newRole['role']) {
                case Role::NAME_SERVICE_WORKER:
                    if (!$user->canMakeServiceAdmin($service)) {
                        return false;
                    }
                    break;
                case Role::NAME_SERVICE_ADMIN:
                    if (!$user->canMakeServiceAdmin($service)) {
                        return false;
                    }
                    break;
                case Role::NAME_ORGANISATION_ADMIN:
                    if (!$user->canMakeOrganisationAdmin($organisation)) {
                        return false;
                    }
                    break;
                case Role::NAME_GLOBAL_ADMIN:
                    if (!$user->canMakeGlobalAdmin()) {
                        return false;
                    }
                    break;
                case Role::NAME_SUPER_ADMIN:
                    if (!$user->canMakeSuperAdmin()) {
                        return false;
                    }
                    break;
            }
        }

        return true;
    }

    /**
     * @param array $deletedRoles
     * @return bool
     */
    protected function canRevokeDeletedRoles(array $deletedRoles): bool
    {
        /** @var \App\Models\User $user */
        $user = $this->user()->load('userRoles');

        /** @var \App\Models\User $subject */
        $subject = $this->route('user')->load('userRoles');

        foreach ($deletedRoles as $deletedRole) {
            $service = isset($deletedRole['service_id']) ? Service::findOrFail($deletedRole['service_id']) : null;
            $organisation = isset($deletedRole['organisation_id']) ? Organisation::findOrFail($deletedRole['organisation_id']) : null;

            switch ($deletedRole['role']) {
                case Role::NAME_SERVICE_WORKER:
                    if (!$user->canRevokeServiceWorker($subject, $service)) {
                        return false;
                    }
                    break;
                case Role::NAME_SERVICE_ADMIN:
                    if (!$user->canRevokeServiceAdmin($subject, $service)) {
                        return false;
                    }
                    break;
                case Role::NAME_ORGANISATION_ADMIN:
                    if (!$user->canRevokeOrganisationAdmin($subject, $organisation)) {
                        return false;
                    }
                    break;
                case Role::NAME_GLOBAL_ADMIN:
                    if (!$user->canRevokeGlobalAdmin($subject)) {
                        return false;
                    }
                    break;
                case Role::NAME_SUPER_ADMIN:
                    if (!$user->canRevokeSuperAdmin($subject)) {
                        return false;
                    }
                    break;
            }
        }

        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'first_name' => ['required', 'string', 'min:1', 'max:255'],
            'last_name' => ['required', 'string', 'min:1', 'max:255'],
            'email' => ['required', 'email', 'max:255', Rule::unique('users', 'email')->ignoreModel($this->route('user'))],
            'phone' => ['required', 'string', 'min:1', 'max:255'],
            'password' => ['string', 'min:8', 'max:255'],

            'roles' => ['required', 'array'],
            'roles.*' => ['required', 'array'],
            'roles.*.role' => ['required_with:roles.*', 'string', 'exists:roles,name'],
            'roles.*.organisation_id' => [
                'required_if:roles.*.role,'.Role::NAME_ORGANISATION_ADMIN,
                'exists:organisations,id',
            ],
            'roles.*.service_id' => [
                'required_if:roles.*.role,'.Role::NAME_SERVICE_WORKER,
                'required_if:roles.*.role,'.Role::NAME_SERVICE_ADMIN,
                'exists:services,id',
            ],
        ];
    }
}
