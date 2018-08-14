<?php

namespace App\Geocode;

use App\Contracts\Geocoder;
use GuzzleHttp\Client;

class GoogleGeocoder implements Geocoder
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
     * @param string $address
     * @return \App\Geocode\Coordinate
     * @throws \App\Geocode\AddressNotFoundException
     */
    public function geocode(string $address): Coordinate
    {
        // Make the request.
        $response = $this->client->get('/maps/api/geocode/json', [
            'query' => [
                'address' => $address,
                'key' => $this->apiKey,
            ]
        ]);

        // Parse the results.
        $results = json_decode($response->getBody()->getContents(), true)['results'];

        // Throw an exception if no address was found.
        if (count($results) === 0) {
            throw new AddressNotFoundException();
        }

        // Get the latitude and longitude.
        $location = $results[0]['geometry']['location'];

        return new Coordinate($location['lat'], $location['lng']);
    }
}
