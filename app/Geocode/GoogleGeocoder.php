<?php

namespace App\Geocode;

use App\Support\Address;
use App\Support\Coordinate;
use GuzzleHttp\Client;

class GoogleGeocoder extends Geocoder
{
    /**
     * @var \GuzzleHttp\Client
     */
    protected $client;

    /**
     * @var string
     */
    protected $apiKey;

    /**
     * GoogleGeocoder constructor.
     */
    public function __construct()
    {
        $this->client = new Client(['base_uri' => 'https://maps.googleapis.com/']);
        $this->apiKey = config('ck.google_api_key');
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
        $response = $this->client->get('/maps/api/geocode/json', [
            'query' => [
                'address' => $this->normaliseAddress($address),
                'key' => $this->apiKey,
            ],
        ]);

        // Parse the results.
        $results = json_decode($response->getBody()->getContents(), true)['results'];

        // Throw an exception if no address was found.
        if (count($results) === 0) {
            $this->saveToCache($address, null);

            throw new AddressNotFoundException();
        }

        // Get the latitude and longitude.
        $location = $results[0]['geometry']['location'];
        $coordinate = new Coordinate($location['lat'], $location['lng']);

        // Save to cache.
        $this->saveToCache($address, $coordinate);

        return $coordinate;
    }
}
