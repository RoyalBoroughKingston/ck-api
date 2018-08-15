<?php

namespace App\Contracts;

use App\Geocode\Coordinate;
use App\Support\Address;

interface Geocoder
{
    /**
     * Convert a a textual address into a coordinate.
     *
     * @param \App\Support\Address $address
     * @return \App\Geocode\Coordinate
     */
    public function geocode(Address $address): Coordinate;
}
