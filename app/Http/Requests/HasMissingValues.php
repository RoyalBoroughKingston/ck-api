<?php

namespace App\Http\Requests;

use App\Support\MissingValue;

trait HasMissingValues
{
    /**
     * @param string $key
     * @param callable|null $pipe if the value exists then pass through this function
     * @return mixed|\App\Support\MissingValue
     */
    public function missing(string $key, callable $pipe = null)
    {
        if ($this->has($key)) {
            return $pipe ? $pipe($this->input($key)) : $this->input($key);
        }

        return new MissingValue();
    }
}
