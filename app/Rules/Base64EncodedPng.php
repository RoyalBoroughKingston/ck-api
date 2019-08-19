<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;

class Base64EncodedPng implements Rule
{
    /**
     * @var bool
     */
    protected $nullable;

    /**
     * Base64EncodedPng constructor.
     *
     * @param bool $nullable
     */
    public function __construct(bool $nullable = false)
    {
        $this->nullable = $nullable;
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
        if ($this->nullable && $value === null) {
            return true;
        }

        // Immediately fail if the value is not a string.
        if (!is_string($value)) {
            return false;
        }

        return preg_match('/^(data:image\/png;base64,)/', $value) > 0;
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return 'The :attribute field must be a Base64 encoded string of a PNG image.';
    }
}
