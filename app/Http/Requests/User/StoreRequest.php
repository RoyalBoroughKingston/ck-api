<?php

namespace App\Http\Requests\User;

use App\Models\Role;
use App\Rules\CanAssignRoleToUser;
use App\Rules\Password;
use App\Rules\UkPhoneNumber;
use App\Rules\UserEmailNotTaken;
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
            'email' => ['required', 'email', 'max:255', new UserEmailNotTaken()],
            'phone' => ['required', 'string', 'min:1', 'max:255', new UkPhoneNumber()],
            'password' => ['required', 'string', 'min:8', 'max:255', new Password()],

            'roles' => ['required', 'array'],
            'roles.*' => ['required', 'array', new CanAssignRoleToUser($this->user()->load('userRoles'))],
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
