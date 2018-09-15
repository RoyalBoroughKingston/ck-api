<?php

namespace App\Http\Requests\User;

use App\Models\Organisation;
use App\Models\Role;
use App\Models\Service;
use App\Rules\Password;
use App\Rules\UkPhoneNumber;
use Illuminate\Foundation\Http\FormRequest;

class StoreRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        /** @var \App\Models\User $user */
        $user = $this->user()->load('userRoles');

        foreach ($this->roles as $role) {
            switch ($role['role']) {
                case Role::NAME_SERVICE_WORKER:
                    $service = Service::findOrFail($role['service_id']);
                    if (!$user->canMakeServiceWorker($service)) {
                        return false;
                    }
                    break;
                case Role::NAME_SERVICE_ADMIN:
                    $service = Service::findOrFail($role['service_id']);
                    if (!$user->canMakeServiceAdmin($service)) {
                        return false;
                    }
                    break;
                case Role::NAME_ORGANISATION_ADMIN:
                    $organisation = Organisation::findOrFail($role['organisation_id']);
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

        // User must at least be a service worker.
        if ($this->user()->isGlobalAdmin() || $this->user()->isServiceWorker()) {
            return true;
        }

        return false;
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
            'email' => ['required', 'email', 'max:255', 'unique:users,email'],
            'phone' => ['required', 'string', 'min:1', 'max:255', new UkPhoneNumber()],
            'password' => ['required', 'string', 'min:8', 'max:255', new Password()],

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
