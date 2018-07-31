<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;

class Base64EncodedPng implements Rule
{
    /**
     * Determine if the validation rule passes.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @return bool
     */
    public function passes($attribute, $value)
    {
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
