<?php

namespace App\Geocode;

use App\Support\Address;
use App\Support\Coordinate;

class StubGeocoder extends Geocoder
{
    /**
     * Convert a a textual address into a coordinate.
     *
     * @param \App\Support\Address $address
     * @return \App\Support\Coordinate
     */
    public function geocode(Address $address): Coordinate
    {
        // Return coordinates for Leeds, UK.
        return new Coordinate(rand(-90, 90), rand(-180, 180));
    }
}
