<?php

namespace App\Geocode;

use App\Support\Address;

class StubGeocoder extends Geocoder
{
    /**
     * Convert a a textual address into a coordinate.
     *
     * @param \App\Support\Address $address
     * @return \App\Geocode\Coordinate
     */
    public function geocode(Address $address): Coordinate
    {
        // Return coordinates for Leeds, UK.
        return new Coordinate(rand(-90, 90), rand(-180, 180));
    }
}
