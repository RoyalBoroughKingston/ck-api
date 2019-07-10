<?php

namespace App\Rules;

use App\Models\Service;
use App\Models\User;
use Illuminate\Contracts\Validation\Rule;

class IsServiceAdmin implements Rule
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

        $service = Service::query()->find($value);

        return $service ? $this->user->isServiceAdmin($service) : false;
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return 'The :attribute field must contain an ID for a service you are a service admin for.';
    }
}
