<?php

namespace App\Models\Mutators;

trait SettingMutators
{
    /**
     * @param string $value
     * @return mixed
     */
    public function getValueAttribute(string $value)
    {
        return json_decode($value, true);
    }

    /**
     * @param mixed $value
     */
    public function setValueAttribute($value): void
    {
        $this->attributes['value'] = json_encode($value);
    }
}
