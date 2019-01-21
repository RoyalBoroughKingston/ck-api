<?php

namespace Tests\Feature;

use App\Events\EndpointHit;
use App\Models\Audit;
use App\Models\Location;
use App\Models\Organisation;
use App\Models\Service;
use App\Models\UpdateRequest;
use App\Models\User;
use Illuminate\Http\Response;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Event;
use Laravel\Passport\Passport;
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
            'has_wheelchair_access',
            'has_induction_loop',
            'created_at',
            'updated_at',
        ]);
        $response->assertJsonFragment([
            'id' => $location->id,
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
            'has_wheelchair_access' => $location->has_wheelchair_access,
            'has_induction_loop' => $location->has_induction_loop,
            'created_at' => $location->created_at->format(Carbon::ISO8601),
            'updated_at' => $location->updated_at->format(Carbon::ISO8601),
        ]);
    }

    public function test_audit_created_when_listed()
    {
        $this->fakeEvents();

        $this->json('GET', '/core/v1/locations');

        Event::assertDispatched(EndpointHit::class, function (EndpointHit $event) {
            return ($event->getAction() === Audit::ACTION_READ);
        });
    }

    /*
     * Create a location.
     */

    public function test_guest_cannot_create_one()
    {
        $response = $this->json('POST', '/core/v1/locations');

        $response->assertStatus(Response::HTTP_UNAUTHORIZED);
    }

    public function test_service_worker_cannot_create_one()
    {
        /**
         * @var \App\Models\Service $service
         * @var \App\Models\User $user
         */
        $service = factory(Service::class)->create();
        $user = factory(User::class)->create();
        $user->makeServiceWorker($service);

        Passport::actingAs($user);

        $response = $this->json('POST', '/core/v1/locations');

        $response->assertStatus(Response::HTTP_FORBIDDEN);
    }

    public function test_service_admin_can_create_one()
    {
        /**
         * @var \App\Models\Service $service
         * @var \App\Models\User $user
         */
        $service = factory(Service::class)->create();
        $user = factory(User::class)->create();
        $user->makeServiceAdmin($service);

        Passport::actingAs($user);

        $response = $this->json('POST', '/core/v1/locations', [
            'address_line_1' => '30-34 Aire St',
            'address_line_2' => null,
            'address_line_3' => null,
            'city' => 'Leeds',
            'county' => 'West Yorkshire',
            'postcode' => 'LS1 4HT',
            'country' => 'England',
            'accessibility_info' => null,
            'has_wheelchair_access' => false,
            'has_induction_loop' => false,
        ]);

        $response->assertStatus(Response::HTTP_CREATED);
        $response->assertJsonFragment([
            'address_line_1' => '30-34 Aire St',
            'address_line_2' => null,
            'address_line_3' => null,
            'city' => 'Leeds',
            'county' => 'West Yorkshire',
            'postcode' => 'LS1 4HT',
            'country' => 'England',
            'accessibility_info' => null,
            'has_wheelchair_access' => false,
            'has_induction_loop' => false,
        ]);
    }

    public function test_audit_created_when_created()
    {
        $this->fakeEvents();

        /**
         * @var \App\Models\Service $service
         * @var \App\Models\User $user
         */
        $service = factory(Service::class)->create();
        $user = factory(User::class)->create();
        $user->makeServiceAdmin($service);

        Passport::actingAs($user);

        $response = $this->json('POST', '/core/v1/locations', [
            'address_line_1' => '30-34 Aire St',
            'address_line_2' => null,
            'address_line_3' => null,
            'city' => 'Leeds',
            'county' => 'West Yorkshire',
            'postcode' => 'LS1 4HT',
            'country' => 'England',
            'accessibility_info' => null,
            'has_wheelchair_access' => false,
            'has_induction_loop' => false,
        ]);

        Event::assertDispatched(EndpointHit::class, function (EndpointHit $event) use ($user, $response) {
            return ($event->getAction() === Audit::ACTION_CREATE) &&
                ($event->getUser()->id === $user->id) &&
                ($event->getModel()->id === $this->getResponseContent($response)['data']['id']);
        });
    }

    /*
     * Get a specific location.
     */

    public function test_guest_can_view_one()
    {
        $location = factory(Location::class)->create();

        $response = $this->json('GET', "/core/v1/locations/{$location->id}");

        $response->assertStatus(Response::HTTP_OK);
        $response->assertJsonFragment([
            'id' => $location->id,
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
            'has_wheelchair_access' => $location->has_wheelchair_access,
            'has_induction_loop' => $location->has_induction_loop,
            'created_at' => $location->created_at->format(Carbon::ISO8601),
            'updated_at' => $location->updated_at->format(Carbon::ISO8601),
        ]);
    }

    public function test_audit_created_when_viewed()
    {
        $this->fakeEvents();

        $location = factory(Location::class)->create();

        $this->json('GET', "/core/v1/locations/{$location->id}");

        Event::assertDispatched(EndpointHit::class, function (EndpointHit $event) use ($location) {
            return ($event->getAction() === Audit::ACTION_READ) &&
                ($event->getModel()->id === $location->id);
        });
    }

    /*
     * Update a specific location.
     */

    public function test_guest_cannot_update_one()
    {
        $location = factory(Location::class)->create();

        $response = $this->json('PUT', "/core/v1/locations/{$location->id}");

        $response->assertStatus(Response::HTTP_UNAUTHORIZED);
    }

    public function test_service_worker_cannot_update_one()
    {
        /**
         * @var \App\Models\Service $service
         * @var \App\Models\User $user
         */
        $service = factory(Service::class)->create();
        $user = factory(User::class)->create();
        $user->makeServiceWorker($service);
        $location = factory(Location::class)->create();

        Passport::actingAs($user);

        $response = $this->json('PUT', "/core/v1/locations/{$location->id}");

        $response->assertStatus(Response::HTTP_FORBIDDEN);
    }

    public function test_service_admin_can_request_to_update_one()
    {
        /**
         * @var \App\Models\Service $service
         * @var \App\Models\User $user
         */
        $service = factory(Service::class)->create();
        $user = factory(User::class)->create();
        $user->makeServiceAdmin($service);
        $location = factory(Location::class)->create();

        Passport::actingAs($user);

        $payload = [
            'address_line_1' => '30-34 Aire St',
            'address_line_2' => null,
            'address_line_3' => null,
            'city' => 'Leeds',
            'county' => 'West Yorkshire',
            'postcode' => 'LS1 4HT',
            'country' => 'England',
            'accessibility_info' => null,
            'has_wheelchair_access' => false,
            'has_induction_loop' => false,
        ];
        $response = $this->json('PUT', "/core/v1/locations/{$location->id}", $payload);

        $response->assertStatus(Response::HTTP_OK);
        $response->assertJsonFragment(['data' => $payload]);
        $this->assertDatabaseHas((new UpdateRequest())->getTable(), [
            'user_id' => $user->id,
            'updateable_type' => 'locations',
            'updateable_id' => $location->id,
        ]);
        $data = UpdateRequest::where('updateable_type', 'locations')
            ->where('updateable_id', $location->id)
            ->firstOrFail()
            ->data;
        $this->assertEquals($data, $payload);
    }

    public function test_audit_created_when_updated()
    {
        $this->fakeEvents();

        /**
         * @var \App\Models\Service $service
         * @var \App\Models\User $user
         */
        $service = factory(Service::class)->create();
        $user = factory(User::class)->create();
        $user->makeServiceAdmin($service);
        $location = factory(Location::class)->create();

        Passport::actingAs($user);

        $this->json('PUT', "/core/v1/locations/{$location->id}", [
            'address_line_1' => '30-34 Aire St',
            'address_line_2' => null,
            'address_line_3' => null,
            'city' => 'Leeds',
            'county' => 'West Yorkshire',
            'postcode' => 'LS1 4HT',
            'country' => 'England',
            'accessibility_info' => null,
            'has_wheelchair_access' => false,
            'has_induction_loop' => false,
        ]);

        Event::assertDispatched(EndpointHit::class, function (EndpointHit $event) use ($user, $location) {
            return ($event->getAction() === Audit::ACTION_UPDATE) &&
                ($event->getUser()->id === $user->id) &&
                ($event->getModel()->id === $location->id);
        });
    }

    public function test_only_partial_fields_can_be_updated()
    {
        /**
         * @var \App\Models\Service $service
         * @var \App\Models\User $user
         */
        $service = factory(Service::class)->create();
        $user = factory(User::class)->create();
        $user->makeServiceAdmin($service);
        $location = factory(Location::class)->create();

        Passport::actingAs($user);

        $payload = [
            'address_line_1' => '30-34 Aire St',
        ];
        $response = $this->json('PUT', "/core/v1/locations/{$location->id}", $payload);

        $response->assertStatus(Response::HTTP_OK);
        $response->assertJsonFragment(['data' => $payload]);
        $this->assertDatabaseHas((new UpdateRequest())->getTable(), [
            'user_id' => $user->id,
            'updateable_type' => 'locations',
            'updateable_id' => $location->id,
        ]);
        $data = UpdateRequest::where('updateable_type', 'locations')
            ->where('updateable_id', $location->id)
            ->firstOrFail()
            ->data;
        $this->assertEquals($data, $payload);
    }

    /*
     * Delete a specific location.
     */

    public function test_guest_cannot_delete_one()
    {
        $location = factory(Location::class)->create();

        $response = $this->json('DELETE', "/core/v1/locations/{$location->id}");

        $response->assertStatus(Response::HTTP_UNAUTHORIZED);
    }

    public function test_service_worker_cannot_delete_one()
    {
        /**
         * @var \App\Models\Service $service
         * @var \App\Models\User $user
         */
        $service = factory(Service::class)->create();
        $user = factory(User::class)->create();
        $user->makeServiceWorker($service);
        $location = factory(Location::class)->create();

        Passport::actingAs($user);

        $response = $this->json('DELETE', "/core/v1/locations/{$location->id}");

        $response->assertStatus(Response::HTTP_FORBIDDEN);
    }

    public function test_service_admin_cannot_delete_one()
    {
        /**
         * @var \App\Models\Service $service
         * @var \App\Models\User $user
         */
        $service = factory(Service::class)->create();
        $user = factory(User::class)->create();
        $user->makeServiceAdmin($service);
        $location = factory(Location::class)->create();

        Passport::actingAs($user);

        $response = $this->json('DELETE', "/core/v1/locations/{$location->id}");

        $response->assertStatus(Response::HTTP_FORBIDDEN);
    }

    public function test_organisation_admin_cannot_delete_one()
    {
        /**
         * @var \App\Models\Organisation $organisation
         * @var \App\Models\User $user
         */
        $organisation = factory(Organisation::class)->create();
        $user = factory(User::class)->create();
        $user->makeOrganisationAdmin($organisation);
        $location = factory(Location::class)->create();

        Passport::actingAs($user);

        $response = $this->json('DELETE', "/core/v1/locations/{$location->id}");

        $response->assertStatus(Response::HTTP_FORBIDDEN);
    }

    public function test_global_admin_can_delete_one()
    {
        /**
         * @var \App\Models\User $user
         */
        $user = factory(User::class)->create();
        $user->makeGlobalAdmin();
        $location = factory(Location::class)->create();

        Passport::actingAs($user);

        $response = $this->json('DELETE', "/core/v1/locations/{$location->id}");

        $response->assertStatus(Response::HTTP_OK);
        $this->assertDatabaseMissing((new Location())->getTable(), ['id' => $location->id]);
    }

    public function test_audit_created_when_deleted()
    {
        $this->fakeEvents();

        /**
         * @var \App\Models\User $user
         */
        $user = factory(User::class)->create();
        $user->makeGlobalAdmin();
        $location = factory(Location::class)->create();

        Passport::actingAs($user);

        $this->json('DELETE', "/core/v1/locations/{$location->id}");

        Event::assertDispatched(EndpointHit::class, function (EndpointHit $event) use ($user, $location) {
            return ($event->getAction() === Audit::ACTION_DELETE) &&
                ($event->getUser()->id === $user->id) &&
                ($event->getModel()->id === $location->id);
        });
    }
}
