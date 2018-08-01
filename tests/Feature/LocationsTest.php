<?php

namespace Tests\Feature;

use App\Models\Location;
use Illuminate\Http\Response;
use Illuminate\Support\Carbon;
use Tests\TestCase;

class LocationsTest extends TestCase
{
    /*
     * List all the locations.
     */

    public function test_guest_can_list_them()
    {
        $location = factory(Location::class)->create();

        $response = $this->json('GET', '/core/v1/locations');

        $response->assertStatus(Response::HTTP_OK);
        $response->assertJsonCollection([
            'id',
            'service_id',
            'address_line_1',
            'address_line_2',
            'address_line_3',
            'city',
            'county',
            'postcode',
            'country',
            'lat',
            'lon',
            'accessibility_info',
            'created_at',
            'updated_at',
        ]);
        $response->assertJsonFragment([
            'id' => $location->id,
            'service_id' => $location->service_id,
            'address_line_1' => $location->address_line_1,
            'address_line_2' => $location->address_line_2,
            'address_line_3' => $location->address_line_3,
            'city' => $location->city,
            'county' => $location->county,
            'postcode' => $location->postcode,
            'country' => $location->country,
            'lat' => $location->lat,
            'lon' => $location->lon,
            'accessibility_info' => $location->accessibility_info,
            'created_at' => $location->created_at->format(Carbon::ISO8601),
            'updated_at' => $location->updated_at->format(Carbon::ISO8601),
        ]);
    }
}
