<?php

namespace App\Models\Mutators;

trait LocationMutators
{
    /**
     * @return string
     */
    public function getFullAddressAttribute(): string
    {
        $parts = [
            $this->address_line_1,
            $this->address_line_2,
            $this->address_line_3,
            $this->city,
            $this->county,
            $this->postcode,
            $this->country,
        ];

        $parts = array_filter_null($parts);

        return implode(', ', $parts);
    }
}
