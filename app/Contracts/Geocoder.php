<?php

namespace App\Contracts;

use App\Geocode\Coordinate;

interface Geocoder
{
    /**
     * Convert a a textual address into a coordinate.
     *
     * @param string $address
     * @return \App\Geocode\Coordinate
     * @throws \App\Geocode\AddressNotFoundException
     */
    public function geocode(string $address): Coordinate;
}
