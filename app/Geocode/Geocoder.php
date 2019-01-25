<?php

namespace App\Geocode;

use App\Contracts\Geocoder as GeocoderContract;
use App\Models\CachedGeocodeResult;
use App\Support\Address;
use App\Support\Coordinate;

abstract class Geocoder implements GeocoderContract
{
    /**
     * @param \App\Support\Address $address
     * @return \App\Support\Coordinate|null
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
     * @param \App\Support\Coordinate|null $coordinate
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
        $postcode = mb_strtolower($address->postcode);
        $postcode = single_space($postcode);

        $country = mb_strtolower($address->country);
        $country = single_space($country);

        return "$postcode, $country";
    }
}
