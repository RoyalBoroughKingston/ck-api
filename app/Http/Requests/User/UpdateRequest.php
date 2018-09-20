<?php

namespace App\Http\Requests\User;

use App\Models\Organisation;
use App\Models\Role;
use App\Models\Service;
use App\Models\UserRole;
use App\Rules\CanAssignRoleToUser;
use App\Rules\CanRevokeRoleFromUser;
use App\Rules\Password;
use App\Rules\UkPhoneNumber;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateRequest extends FormRequest
{
    /**
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

            $existingRolesArray = $exitingRoles->map(function (UserRole $userRole) {
                return array_filter_null([
                    'role' => $userRole->role->name,
                    'organisation_id' => $userRole->organisation_id,
                    'service_id' => $userRole->service_id,
                ]);
            })->toArray();

            $this->existingRoles = $existingRolesArray;
        }

        return $this->existingRoles;
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
            'email' => ['required', 'email', 'max:255', Rule::unique('users', 'email')->ignoreModel($this->user)],
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
