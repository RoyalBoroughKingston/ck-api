<?php

namespace App\Geocode;

use App\Contracts\Geocoder as GeocoderContract;
use App\Models\CachedGeocodeResult;
use App\Support\Address;

abstract class Geocoder implements GeocoderContract
{
    /**
     * @param \App\Support\Address $address
     * @return \App\Geocode\Coordinate|null
     */
    protected function retrieveFromCache(Address $address): ?Coordinate
    {
        $cachedGeocodeResult = CachedGeocodeResult::where('query', $this->normaliseAddress($address))->first();

        if ($cachedGeocodeResult === null || $cachedGeocodeResult->hasNoCoordinate()) {
            return null;
        }

        return $cachedGeocodeResult->toCoordinate();
    }

    /**
     * @param \App\Support\Address $address
     * @param \App\Geocode\Coordinate|null $coordinate
     * @return \App\Models\CachedGeocodeResult
     */
    protected function saveToCache(Address $address, ?Coordinate $coordinate): CachedGeocodeResult
    {
        return CachedGeocodeResult::create([
            'query' => $this->normaliseAddress($address),
            'lat' => $coordinate ? $coordinate->lat() : null,
            'lon' => $coordinate ? $coordinate->lon() : null,
        ]);
    }

    /**
     * @param \App\Support\Address $address
     * @return string
     */
    protected function normaliseAddress(Address $address): string
    {
        $postcode = strtolower($address->postcode);
        $postcode = strip_spaces($postcode);

        $country = strtolower($address->country);
        $country = single_space($country);

        return "$postcode, $country";
    }
}
