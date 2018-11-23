<?php

namespace App\Models\Mutators;

trait UserMutators
{
    /**
     * @return string
     */
    public function getFullNameAttribute(): string
    {
        return "{$this->first_name} {$this->last_name}";
    }
}
