<?php

namespace App\Http\Middleware;

use Illuminate\Foundation\Http\Middleware\TransformsRequest;

class TrimQuotes extends TransformsRequest
{
    /**
     * The attributes that should not be trimmed of quotes.
     *
     * @var array
     */
    protected $except = [
        //
    ];

    /**
     * Transform the given value.
     *
     * @param  string  $key
     * @param  mixed  $value
     * @return mixed
     */
    protected function transform($key, $value)
    {
        if (in_array($key, $this->except, true)) {
            return $value;
        }

        return is_string($value) ? trim_quotes($value) : $value;
    }
}
