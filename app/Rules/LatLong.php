<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;

class LatLong implements Rule
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
        $parts = explode(',', $value);

        if (count($parts) !== 2) {
            return false;
        }

        list($lat, $long) = $parts;
        $latIsValid = $lat >= -90 && $lat <= 90;
        $longIsValid = $long >= -180 && $long <= 180;

        return $latIsValid && $longIsValid;
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return 'The :attribute must be a valid latitude and longitude.';
    }
}
