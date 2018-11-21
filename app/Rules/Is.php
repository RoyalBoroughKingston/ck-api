<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;

class Is implements Rule
{
    /**
     * @var mixed
     */
    protected $value;

    /**
     * @var bool
     */
    protected $strict;

    /**
     * Create a new rule instance.
     *
     * @param mixed $value
     * @param bool $strict
     */
    public function __construct($value, bool $strict = true)
    {
        $this->value = $value;
        $this->strict = $strict;
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
        return $this->strict ? $this->value === $value : $this->value == $value;
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return 'The :attribute is invalid.';
    }
}
