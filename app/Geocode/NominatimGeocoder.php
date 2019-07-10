<?php

namespace App\Geocode;

use App\Support\Address;
use App\Support\Coordinate;
use GuzzleHttp\Client;

class NominatimGeocoder extends Geocoder
{
    /**
     * @var \GuzzleHttp\Client
     */
    protected $client;

    /**
     * GoogleGeocoder constructor.
     */
    public function __construct()
    {
        $this->client = new Client(['base_uri' => 'https://nominatim.openstreetmap.org/']);
    }

    /**
     * Convert a a textual address into a coordinate.
     *
     * @param \App\Support\Address $address
     * @return \App\Support\Coordinate
     */
    public function geocode(Address $address): Coordinate
    {
        // First attempt to retrieve the coordinate from the cache.
        $cachedCoordinate = $this->retrieveFromCache($address);

        if ($cachedCoordinate !== null) {
            return $cachedCoordinate;
        }

        // Make the request.
        $response = $this->client->get('/search', [
            'query' => [
                'format' => 'json',
                'q' => $this->normaliseAddress($address),
                'limit' => 1,
            ],
        ]);

        // Parse the results.
        $results = json_decode($response->getBody()->getContents(), true);

        // Throw an exception if no address was found.
        if (count($results) === 0) {
            $this->saveToCache($address, null);

            throw new AddressNotFoundException();
        }

        // Get the latitude and longitude.
        $location = $results[0];
        $coordinate = new Coordinate($location['lat'], $location['lon']);

        // Save to cache.
        $this->saveToCache($address, $coordinate);

        return $coordinate;
    }
}
