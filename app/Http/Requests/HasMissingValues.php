<?php

namespace App\Http\Requests;

use App\Support\MissingValue;

trait HasMissingValues
{
    /**
     * @param string $key
     * @return mixed|\App\Support\MissingValue
     */
    public function missing(string $key)
    {
        return $this->input($key, new MissingValue());
    }
}
