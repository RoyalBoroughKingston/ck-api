<?php

namespace App\Http\Requests\User;

use App\Models\Role;
use App\Models\UserRole;
use App\Rules\CanAssignRoleToUser;
use App\Rules\CanRevokeRoleFromUser;
use App\Rules\Password;
use App\Rules\UkPhoneNumber;
use App\Rules\UserEmailNotTaken;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Arr;

class UpdateRequest extends FormRequest
{
    /**
     * Cache the existing roles to prevent multiple database queries.
     *
     * @var array|null
     */
    protected $existingRoles = null;

    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        if ($this->user()->canUpdate($this->user)) {
            return true;
        }

        return false;
    }

    /**
     * @return array
     */
    protected function getExistingRoles(): array
    {
        if ($this->existingRoles === null) {
            /** @var \App\Models\User $user */
            $user = $this->user;

            $exitingRoles = $user->userRoles->load('role');

            $existingRolesArray = $exitingRoles
                ->map(function (UserRole $userRole) {
                    return array_filter_null([
                        'role' => $userRole->role->name,
                        'organisation_id' => $userRole->organisation_id,
                        'service_id' => $userRole->service_id,
                    ]);
                })
                ->toArray();

            $this->existingRoles = $existingRolesArray;
        }

        return $this->existingRoles;
    }

    /**
     * @param array $roles
     * @return array
     */
    protected function parseRoles(array $roles): array
    {
        foreach ($roles as &$role) {
            switch ($role['role']) {
                case Role::NAME_SERVICE_WORKER:
                case Role::NAME_SERVICE_ADMIN:
                    unset($role['organisation_id']);
                    break;
                case Role::NAME_ORGANISATION_ADMIN:
                    unset($role['service_id']);
                    break;
                case Role::NAME_GLOBAL_ADMIN:
                case Role::NAME_SUPER_ADMIN:
                    unset($role['service_id'], $role['organisation_id']);

                    break;
            }
        }

        return $roles;
    }

    /**
     * @return array
     */
    public function getNewRoles(): array
    {
        return array_diff_multi($this->parseRoles($this->roles), $this->getExistingRoles());
    }

    /**
     * @return array
     */
    public function getDeletedRoles(): array
    {
        return array_diff_multi($this->getExistingRoles(), $this->parseRoles($this->roles));
    }

    /**
     * Orders the roles array with the highest first.
     *
     * @param array $roles
     * @return array
     */
    public function orderRoles(array $roles): array
    {
        return Arr::sort($roles, function (array $role) {
            switch ($role['role']) {
                case Role::NAME_SUPER_ADMIN:
                    return 1;
                case Role::NAME_GLOBAL_ADMIN:
                    return 2;
                case Role::NAME_ORGANISATION_ADMIN:
                    return 3;
                case Role::NAME_SERVICE_ADMIN:
                    return 4;
                case Role::NAME_SERVICE_WORKER:
                    return 5;
                default:
                    return 6;
            }
        });
    }

    /**
     * @return bool
     */
    public function rolesHaveBeenUpdated(): bool
    {
        $hasNewRoles = count($this->getNewRoles()) > 0;
        $hasDeletedRoles = count($this->getDeletedRoles()) > 0;

        return $hasNewRoles || $hasDeletedRoles;
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
            'email' => ['required', 'email', 'max:255', new UserEmailNotTaken($this->user)],
            'phone' => ['required', 'string', 'min:1', 'max:255', new UkPhoneNumber()],
            'password' => ['string', 'min:8', 'max:255', new Password()],

            'roles' => ['required', 'array'],
            'roles.*' => [
                'required',
                'array',
                new CanAssignRoleToUser($this->user()->load('userRoles'), $this->getNewRoles()),
                new CanRevokeRoleFromUser($this->user()->load('userRoles'), $this->user->load('userRoles'), $this->getDeletedRoles()),
            ],
            'roles.*.role' => ['required_with:roles.*', 'string', 'exists:roles,name'],
            'roles.*.organisation_id' => [
                'required_if:roles.*.role,' . Role::NAME_ORGANISATION_ADMIN,
                'exists:organisations,id',
            ],
            'roles.*.service_id' => [
                'required_if:roles.*.role,' . Role::NAME_SERVICE_WORKER,
                'required_if:roles.*.role,' . Role::NAME_SERVICE_ADMIN,
                'exists:services,id',
            ],
        ];
    }
}
