<?php

namespace App\Rules;

use App\Models\User;
use Illuminate\Contracts\Validation\Rule;
use Illuminate\Database\Eloquent\Builder;

class UserEmailNotTaken implements Rule
{
    /**
     * @var \App\Models\User|null
     */
    protected $excludedUser;

    /**
     * @var string|null
     */
    protected $message;

    /**
     * Create a new rule instance.
     *
     * @param \App\Models\User|null $excludedUser
     * @param string|null $message
     */
    public function __construct(User $excludedUser = null, ?string $message = null)
    {
        $this->excludedUser = $excludedUser;
        $this->message = $message;
    }

    /**
     * Determine if the validation rule passes.
     *
     * @param string $attribute
     * @param mixed $email
     * @return bool
     */
    public function passes($attribute, $email)
    {
        if (!is_string($email)) {
            return false;
        }

        return User::query()
            ->where('email', $email)
            ->when($this->excludedUser, function (Builder $query): Builder {
                return $query->where('id', '!=', $this->excludedUser->id);
            })
            ->doesntExist();
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return $this->message ?? 'This email address has already been taken.';
    }
}
