<?php

namespace Tests\Feature;

use App\Models\HolidayOpeningHour;
use App\Models\Location;
use App\Models\RegularOpeningHour;
use App\Models\Service;
use App\Models\ServiceLocation;
use App\Models\User;
use Illuminate\Http\Response;
use Illuminate\Support\Carbon;
use Laravel\Passport\Passport;
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

    public function test_guest_cannot_create_one()
    {
        $response = $this->json('POST', '/core/v1/service-locations');

        $response->assertStatus(Response::HTTP_UNAUTHORIZED);
    }

    public function test_service_worker_cannot_create_one()
    {
        $service = factory(Service::class)->create();
        $user = factory(User::class)->create()->makeServiceWorker($service);

        Passport::actingAs($user);

        $response = $this->json('POST', '/core/v1/service-locations');

        $response->assertStatus(Response::HTTP_FORBIDDEN);
    }

    public function test_service_admin_for_another_service_cannot_create_one()
    {
        $location = factory(Location::class)->create();
        $service = factory(Service::class)->create();
        $anotherService = factory(Service::class)->create();
        $user = factory(User::class)->create()->makeServiceAdmin($anotherService);

        Passport::actingAs($user);

        $response = $this->json('POST', '/core/v1/service-locations', [
            'service_id' => $service->id,
            'location_id' => $location->id,
            'name' => null,
            'regular_opening_hours' => [],
            'holiday_opening_hours' => [],
        ]);

        $response->assertStatus(Response::HTTP_FORBIDDEN);
    }

    public function test_service_admin_can_create_one()
    {
        $location = factory(Location::class)->create();
        $service = factory(Service::class)->create();
        $user = factory(User::class)->create()->makeServiceAdmin($service);

        Passport::actingAs($user);

        $response = $this->json('POST', '/core/v1/service-locations', [
            'service_id' => $service->id,
            'location_id' => $location->id,
            'name' => null,
            'regular_opening_hours' => [],
            'holiday_opening_hours' => [],
        ]);

        $response->assertStatus(Response::HTTP_CREATED);
        $response->assertJsonFragment([
            'service_id' => $service->id,
            'location_id' => $location->id,
            'name' => null,
            'regular_opening_hours' => [],
            'holiday_opening_hours' => [],
        ]);
    }

    public function test_service_admin_can_create_one_with_opening_hours()
    {
        $location = factory(Location::class)->create();
        $service = factory(Service::class)->create();
        $user = factory(User::class)->create()->makeServiceAdmin($service);

        Passport::actingAs($user);

        $response = $this->json('POST', '/core/v1/service-locations', [
            'service_id' => $service->id,
            'location_id' => $location->id,
            'name' => null,
            'regular_opening_hours' => [
                [
                    'frequency' => RegularOpeningHour::FREQUENCY_WEEKLY,
                    'weekday' => RegularOpeningHour::WEEKDAY_FRIDAY,
                    'opens_at' => '09:00:00',
                    'closes_at' => '17:30:00',
                ]
            ],
            'holiday_opening_hours' => [
                [
                    'is_closed' => true,
                    'starts_at' => '2018-12-20',
                    'ends_at' => '2019-01-02',
                    'opens_at' => '00:00:00',
                    'closes_at' => '00:00:00',
                ]
            ],
        ]);

        $response->assertStatus(Response::HTTP_CREATED);
        $response->assertJsonFragment([
            'service_id' => $service->id,
            'location_id' => $location->id,
            'name' => null,
            'regular_opening_hours' => [
                [
                    'frequency' => RegularOpeningHour::FREQUENCY_WEEKLY,
                    'weekday' => RegularOpeningHour::WEEKDAY_FRIDAY,
                    'opens_at' => '09:00:00',
                    'closes_at' => '17:30:00',
                ]
            ],
            'holiday_opening_hours' => [
                [
                    'is_closed' => true,
                    'starts_at' => '2018-12-20',
                    'ends_at' => '2019-01-02',
                    'opens_at' => '00:00:00',
                    'closes_at' => '00:00:00',
                ]
            ],
        ]);
    }

    /*
     * Get a specific service location.
     */

    public function test_guest_can_view_one()
    {
        $location = factory(Location::class)->create();
        $service = factory(Service::class)->create();
        $serviceLocation = $service->serviceLocations()->create(['location_id' => $location->id]);

        $response = $this->json('GET', "/core/v1/service-locations/{$serviceLocation->id}");

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

    public function test_guest_can_view_one_with_opening_hours()
    {
        $location = factory(Location::class)->create();
        $service = factory(Service::class)->create();
        $serviceLocation = $service->serviceLocations()->create(['location_id' => $location->id]);
        $regularOpeningHour = factory(RegularOpeningHour::class)->create(['service_location_id' => $serviceLocation->id]);
        $holidayOpeningHour = factory(HolidayOpeningHour::class)->create(['service_location_id' => $serviceLocation->id]);

        $response = $this->json('GET', "/core/v1/service-locations/{$serviceLocation->id}");

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
     * Update a specific service location.
     */

    public function test_guest_cannot_update_one()
    {
        $serviceLocation = factory(ServiceLocation::class)->create();

        $response = $this->json('PUT', "/core/v1/service-locations/{$serviceLocation->id}");

        $response->assertStatus(Response::HTTP_UNAUTHORIZED);
    }

    public function test_service_worker_cannot_update_one()
    {
        $serviceLocation = factory(ServiceLocation::class)->create();
        $user = factory(User::class)->create()->makeServiceWorker($serviceLocation->service);

        Passport::actingAs($user);

        $response = $this->json('PUT', "/core/v1/service-locations/{$serviceLocation->id}");

        $response->assertStatus(Response::HTTP_FORBIDDEN);
    }

    public function test_service_admin_can_update_one()
    {
        $serviceLocation = factory(ServiceLocation::class)->create();
        $user = factory(User::class)->create()->makeServiceAdmin($serviceLocation->service);

        Passport::actingAs($user);

        $payload = [
            'name' => 'New Company Name',
            'regular_opening_hours' => [
                [
                    'frequency' => RegularOpeningHour::FREQUENCY_MONTHLY,
                    'day_of_month' => 10,
                    'opens_at' => '10:00:00',
                    'closes_at' => '14:00:00',
                ]
            ],
            'holiday_opening_hours' => [
                [
                    'is_closed' => true,
                    'starts_at' => '2018-01-01',
                    'ends_at' => '2018-01-01',
                    'opens_at' => '00:00:00',
                    'closes_at' => '00:00:00',
                ]
            ],
        ];
        $response = $this->json('PUT', "/core/v1/service-locations/{$serviceLocation->id}", $payload);

        $response->assertStatus(Response::HTTP_OK);
        $response->assertJsonFragment(['data' => $payload]);
        $data = $serviceLocation->updateRequests()->firstOrFail()->data;
        $this->assertEquals($data, $payload);
    }

    /*
     * Delete a specific service location.
     */
}
