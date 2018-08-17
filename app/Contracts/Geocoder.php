<?php

namespace App\Contracts;

use App\Support\Coordinate;
use App\Support\Address;

interface Geocoder
{
    /**
     * Convert a a textual address into a coordinate.
     *
     * @param \App\Support\Address $address
     * @return \App\Support\Coordinate
     */
    public function geocode(Address $address): Coordinate;
}
