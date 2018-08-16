<?php

namespace Tests\Feature;

use App\Events\EndpointHit;
use App\Models\Audit;
use App\Models\HolidayOpeningHour;
use App\Models\Location;
use App\Models\RegularOpeningHour;
use App\Models\Service;
use App\Models\ServiceLocation;
use App\Models\User;
use Illuminate\Http\Response;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Event;
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

    public function test_guest_can_list_them_for_service()
    {
        $serviceLocation = factory(ServiceLocation::class)->create();
        $anotherServiceLocation = factory(ServiceLocation::class)->create();

        $response = $this->json('GET', "/core/v1/service-locations?filter[service_id]={$serviceLocation->service_id}");
        $serviceLocation = $serviceLocation->fresh();

        $response->assertStatus(Response::HTTP_OK);
        $response->assertJsonFragment([
            [
                'id' => $serviceLocation->id,
                'service_id' => $serviceLocation->service_id,
                'location_id' => $serviceLocation->location_id,
                'name' => null,
                'is_open_now' => false,
                'regular_opening_hours' => [],
                'holiday_opening_hours' => [],
                'created_at' => $serviceLocation->created_at->format(Carbon::ISO8601),
                'updated_at' => $serviceLocation->updated_at->format(Carbon::ISO8601),
            ]
        ]);
        $response->assertJsonMissing([
            [
                'id' => $anotherServiceLocation->id,
                'service_id' => $anotherServiceLocation->service_id,
                'location_id' => $anotherServiceLocation->location_id,
                'name' => null,
                'is_open_now' => false,
                'regular_opening_hours' => [],
                'holiday_opening_hours' => [],
                'created_at' => $anotherServiceLocation->created_at->format(Carbon::ISO8601),
                'updated_at' => $anotherServiceLocation->updated_at->format(Carbon::ISO8601),
            ]
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

    public function test_audit_created_when_listed()
    {
        $this->fakeEvents();

        $this->json('GET', '/core/v1/service-locations');

        Event::assertDispatched(EndpointHit::class, function (EndpointHit $event) {
            return ($event->getAction() === Audit::ACTION_READ);
        });
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

    public function test_audit_created_when_created()
    {
        $this->fakeEvents();

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

        Event::assertDispatched(EndpointHit::class, function (EndpointHit $event) use ($user, $response) {
            return ($event->getAction() === Audit::ACTION_CREATE) &&
                ($event->getUser()->id === $user->id) &&
                ($event->getModel()->id === $this->getResponseContent($response)['data']['id']);
        });
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

    public function test_audit_created_when_viewed()
    {
        $this->fakeEvents();

        $location = factory(Location::class)->create();
        $service = factory(Service::class)->create();
        $serviceLocation = $service->serviceLocations()->create(['location_id' => $location->id]);

        $this->json('GET', "/core/v1/service-locations/{$serviceLocation->id}");

        Event::assertDispatched(EndpointHit::class, function (EndpointHit $event) use ($serviceLocation) {
            return ($event->getAction() === Audit::ACTION_READ) &&
                ($event->getModel()->id === $serviceLocation->id);
        });
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

    public function test_audit_created_when_updated()
    {
        $this->fakeEvents();

        $serviceLocation = factory(ServiceLocation::class)->create();
        $user = factory(User::class)->create()->makeServiceAdmin($serviceLocation->service);

        Passport::actingAs($user);

        $this->json('PUT', "/core/v1/service-locations/{$serviceLocation->id}", [
            'name' => 'New Company Name',
            'regular_opening_hours' => [],
            'holiday_opening_hours' => [],
        ]);

        Event::assertDispatched(EndpointHit::class, function (EndpointHit $event) use ($user, $serviceLocation) {
            return ($event->getAction() === Audit::ACTION_UPDATE) &&
                ($event->getUser()->id === $user->id) &&
                ($event->getModel()->id === $serviceLocation->id);
        });
    }

    /*
     * Delete a specific service location.
     */

    public function test_guest_cannot_delete_one()
    {
        $serviceLocation = factory(ServiceLocation::class)->create();

        $response = $this->json('DELETE', "/core/v1/service-locations/{$serviceLocation->id}");

        $response->assertStatus(Response::HTTP_UNAUTHORIZED);
    }

    public function test_service_worker_cannot_delete_one()
    {
        $serviceLocation = factory(ServiceLocation::class)->create();
        $user = factory(User::class)->create()->makeServiceWorker($serviceLocation->service);

        Passport::actingAs($user);

        $response = $this->json('DELETE', "/core/v1/service-locations/{$serviceLocation->id}");

        $response->assertStatus(Response::HTTP_FORBIDDEN);
    }

    public function test_service_admin_cannot_delete_one()
    {
        $serviceLocation = factory(ServiceLocation::class)->create();
        $user = factory(User::class)->create()->makeServiceAdmin($serviceLocation->service);

        Passport::actingAs($user);

        $response = $this->json('DELETE', "/core/v1/service-locations/{$serviceLocation->id}");

        $response->assertStatus(Response::HTTP_FORBIDDEN);
    }

    public function test_organisation_admin_cannot_delete_one()
    {
        $serviceLocation = factory(ServiceLocation::class)->create();
        $user = factory(User::class)->create()->makeOrganisationAdmin($serviceLocation->service->organisation);

        Passport::actingAs($user);

        $response = $this->json('DELETE', "/core/v1/service-locations/{$serviceLocation->id}");

        $response->assertStatus(Response::HTTP_FORBIDDEN);
    }

    public function test_global_admin_cannot_delete_one()
    {
        $serviceLocation = factory(ServiceLocation::class)->create();
        $user = factory(User::class)->create()->makeGlobalAdmin();

        Passport::actingAs($user);

        $response = $this->json('DELETE', "/core/v1/service-locations/{$serviceLocation->id}");

        $response->assertStatus(Response::HTTP_FORBIDDEN);
    }

    public function test_super_admin_can_delete_one()
    {
        $serviceLocation = factory(ServiceLocation::class)->create();
        $user = factory(User::class)->create()->makeSuperAdmin();

        Passport::actingAs($user);

        $response = $this->json('DELETE', "/core/v1/service-locations/{$serviceLocation->id}");

        $response->assertStatus(Response::HTTP_OK);
        $this->assertDatabaseMissing((new ServiceLocation())->getTable(), ['id' => $serviceLocation->id]);
    }

    public function test_audit_created_when_deleted()
    {
        $this->fakeEvents();

        $serviceLocation = factory(ServiceLocation::class)->create();
        $user = factory(User::class)->create()->makeSuperAdmin();

        Passport::actingAs($user);

        $this->json('DELETE', "/core/v1/service-locations/{$serviceLocation->id}");

        Event::assertDispatched(EndpointHit::class, function (EndpointHit $event) use ($user, $serviceLocation) {
            return ($event->getAction() === Audit::ACTION_DELETE) &&
                ($event->getUser()->id === $user->id) &&
                ($event->getModel()->id === $serviceLocation->id);
        });
    }
}
