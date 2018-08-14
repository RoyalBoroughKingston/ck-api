<?php

namespace App\Geocode;

use App\Contracts\Geocoder;

class StubGeocoder implements Geocoder
{
    /**
     * Convert a a textual address into a coordinate.
     *
     * @param string $address
     * @return \App\Geocode\Coordinate
     * @throws \App\Geocode\AddressNotFoundException
     */
    public function geocode(string $address): Coordinate
    {
        // Return coordinates for Leeds, UK.
        return new Coordinate(53.801277, -1.548567);
    }
}
