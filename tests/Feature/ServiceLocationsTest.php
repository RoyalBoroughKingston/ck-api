<?php

namespace Tests\Feature;

use App\Models\HolidayOpeningHour;
use App\Models\Location;
use App\Models\RegularOpeningHour;
use App\Models\Service;
use Illuminate\Http\Response;
use Illuminate\Support\Carbon;
use Tests\TestCase;

class ServiceLocationsTest extends TestCase
{
    /*
     * List all the service locations.
     */

    public function test_guest_can_list_them()
    {
        $location = factory(Location::class)->create();
        $service = factory(Service::class)->create();
        $serviceLocation = $service->serviceLocations()->create(['location_id' => $location->id]);

        $response = $this->json('GET', '/core/v1/service-locations');

        $response->assertStatus(Response::HTTP_OK);
        $response->assertJsonFragment([
            'id' => $serviceLocation->id,
            'service_id' => $serviceLocation->service_id,
            'location_id' => $serviceLocation->location_id,
            'name' => null,
            'is_open_now' => false,
            'regular_opening_hours' => [],
            'holiday_opening_hours' => [],
            'created_at' => $serviceLocation->created_at->format(Carbon::ISO8601),
        ]);
    }

    public function test_guest_can_list_them_with_opening_hours()
    {
        $location = factory(Location::class)->create();
        $service = factory(Service::class)->create();
        $serviceLocation = $service->serviceLocations()->create(['location_id' => $location->id]);
        $regularOpeningHour = factory(RegularOpeningHour::class)->create(['service_location_id' => $serviceLocation->id]);
        $holidayOpeningHour = factory(HolidayOpeningHour::class)->create(['service_location_id' => $serviceLocation->id]);

        $response = $this->json('GET', '/core/v1/service-locations');

        $response->assertStatus(Response::HTTP_OK);
        $response->assertJsonFragment([
            'id' => $serviceLocation->id,
            'service_id' => $serviceLocation->service_id,
            'location_id' => $serviceLocation->location_id,
            'name' => null,
            'is_open_now' => false,
            'regular_opening_hours' => [
                [
                    'frequency' => $regularOpeningHour->frequency,
                    'weekday' => $regularOpeningHour->weekday,
                    'occurrence_of_month' => null,
                    'starts_at' => null,
                    'opens_at' => $regularOpeningHour->opens_at->toString(),
                    'closes_at' => $regularOpeningHour->closes_at->toString(),
                ]
            ],
            'holiday_opening_hours' => [
                [
                    'is_closed' => $holidayOpeningHour->is_closed,
                    'starts_at' => $holidayOpeningHour->starts_at->toDateString(),
                    'ends_at' => $holidayOpeningHour->ends_at->toDateString(),
                    'opens_at' => $holidayOpeningHour->opens_at->toString(),
                    'closes_at' => $holidayOpeningHour->closes_at->toString(),
                ]
            ],
            'created_at' => $serviceLocation->created_at->format(Carbon::ISO8601),
        ]);
    }

    /*
     * Create a service location.
     */

    /*
     * Get a specific service location.
     */

    /*
     * Update a specific service location.
     */

    /*
     * Delete a specific service location.
     */
}
