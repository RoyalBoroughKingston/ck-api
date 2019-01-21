<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;

class Synonyms implements Rule
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
        foreach ($value as $synonym) {
            if (!is_string($synonym)) {
                return false;
            }
        }

        if (preg_match('/\s/', array_last($value))) {
            return false;
        }

        return true;
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return 'The last synonym must be a single word.';
    }
}
