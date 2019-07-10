<?php

namespace App\Rules;

use App\Models\Organisation;
use App\Models\User;
use Illuminate\Contracts\Validation\Rule;

class IsOrganisationAdmin implements Rule
{
    /**
     * @var \App\Models\User
     */
    protected $user;

    /**
     * Create a new rule instance.
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
     * @param string $attribute
     * @param mixed $value
     * @return bool
     */
    public function passes($attribute, $value)
    {
        // Immediately fail if the value is not a string.
        if (!is_string($value)) {
            return false;
        }

        $organisation = Organisation::query()->find($value);

        return $organisation ? $this->user->isOrganisationAdmin($organisation) : false;
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return 'The :attribute field must contain an ID for an organisation you are an organisation admin for.';
    }
}
