<?php

namespace Tests\Feature;

use App\Models\Location;
use App\Models\Organisation;
use App\Models\Service;
use App\Models\ServiceLocation;
use App\Models\UpdateRequest;
use App\Models\User;
use Illuminate\Http\Response;
use Laravel\Passport\Passport;
use Tests\TestCase;

class UpdateRequestsTest extends TestCase
{
    /*
     * List all the update requests.
     */

    public function test_guest_cannot_list_them()
    {
        $response = $this->json('GET', '/core/v1/update-requests');

        $response->assertStatus(Response::HTTP_UNAUTHORIZED);
    }

    public function test_service_worker_cannot_list_them()
    {
        $service = factory(Service::class)->create();
        $user = factory(User::class)->create()->makeServiceWorker($service);

        Passport::actingAs($user);
        $response = $this->json('GET', '/core/v1/update-requests');

        $response->assertStatus(Response::HTTP_FORBIDDEN);
    }

    public function test_service_admin_cannot_list_them()
    {
        $service = factory(Service::class)->create();
        $user = factory(User::class)->create()->makeServiceAdmin($service);

        Passport::actingAs($user);
        $response = $this->json('GET', '/core/v1/update-requests');

        $response->assertStatus(Response::HTTP_FORBIDDEN);
    }

    public function test_organisation_admin_cannot_list_them()
    {
        $organisation = factory(Organisation::class)->create();
        $user = factory(User::class)->create()->makeOrganisationAdmin($organisation);

        Passport::actingAs($user);
        $response = $this->json('GET', '/core/v1/update-requests');

        $response->assertStatus(Response::HTTP_FORBIDDEN);
    }

    public function test_global_admin_can_list_them()
    {
        $user = factory(User::class)->create()->makeGlobalAdmin();
        $location = factory(Location::class)->create();
        $updateRequest = $location->updateRequests()->create([
            'user_id' => $user->id,
            'data' => [
                'address_line_1' => $this->faker->streetAddress,
                'address_line_2' => null,
                'address_line_3' => null,
                'city' => $this->faker->city,
                'county' => 'West Yorkshire',
                'postcode' => $this->faker->postcode,
                'country' => 'United Kingdom',
                'accessibility_info' => null,
            ]
        ]);

        Passport::actingAs($user);
        $response = $this->json('GET', '/core/v1/update-requests');

        $response->assertStatus(Response::HTTP_OK);
        $response->assertJsonFragment([
            'id' => $updateRequest->id,
            'user_id' => $user->id,
            'updateable_type' => 'locations',
            'updateable_id' => $location->id,
            'data' => [
                'address_line_1' => $updateRequest->data['address_line_1'],
                'address_line_2' => null,
                'address_line_3' => null,
                'city' => $updateRequest->data['city'],
                'county' => 'West Yorkshire',
                'postcode' => $updateRequest->data['postcode'],
                'country' => 'United Kingdom',
                'accessibility_info' => null,
            ],
        ]);
    }

    public function test_can_list_them_for_location()
    {
        $user = factory(User::class)->create()->makeGlobalAdmin();
        $location = factory(Location::class)->create();
        $locationUpdateRequest = $location->updateRequests()->create([
            'user_id' => $user->id,
            'data' => [
                'address_line_1' => $this->faker->streetAddress,
                'address_line_2' => null,
                'address_line_3' => null,
                'city' => $this->faker->city,
                'county' => 'West Yorkshire',
                'postcode' => $this->faker->postcode,
                'country' => 'United Kingdom',
                'accessibility_info' => null,
            ]
        ]);
        $organisation = factory(Organisation::class)->create();
        $organisationUpdateRequest = $organisation->updateRequests()->create([
            'user_id' => $user->id,
            'data' => [
                'name' => 'Test Name',
                'description' => 'Lorem ipsum',
                'url' => 'https://example.com',
                'email' => 'phpunit@example.com',
                'phone' => '07700000000',
            ],
        ]);

        Passport::actingAs($user);
        $response = $this->json('GET', "/core/v1/update-requests?filter[location_id]={$location->id}");

        $response->assertStatus(Response::HTTP_OK);
        $response->assertJsonFragment(['id' => $locationUpdateRequest->id]);
        $response->assertJsonMissing(['id' => $organisationUpdateRequest->id]);
    }

    /*
     * Get a specific update request.
     */

    public function test_guest_cannot_view_one()
    {
        $serviceLocation = factory(ServiceLocation::class)->create();
        $updateRequest = $serviceLocation->updateRequests()->create([
            'user_id' => factory(User::class)->create()->id,
            'data' => ['name' => 'Test Name'],
        ]);

        $response = $this->json('GET', "/core/v1/update-requests/{$updateRequest->id}");

        $response->assertStatus(Response::HTTP_UNAUTHORIZED);
    }

    public function test_service_worker_cannot_view_one()
    {
        $service = factory(Service::class)->create();
        $user = factory(User::class)->create()->makeServiceWorker($service);
        Passport::actingAs($user);

        $serviceLocation = factory(ServiceLocation::class)->create();
        $updateRequest = $serviceLocation->updateRequests()->create([
            'user_id' => factory(User::class)->create()->id,
            'data' => ['name' => 'Test Name'],
        ]);

        $response = $this->json('GET', "/core/v1/update-requests/{$updateRequest->id}");

        $response->assertStatus(Response::HTTP_FORBIDDEN);
    }

    public function test_service_admin_cannot_view_one()
    {
        $service = factory(Service::class)->create();
        $user = factory(User::class)->create()->makeServiceAdmin($service);
        Passport::actingAs($user);

        $serviceLocation = factory(ServiceLocation::class)->create();
        $updateRequest = $serviceLocation->updateRequests()->create([
            'user_id' => factory(User::class)->create()->id,
            'data' => ['name' => 'Test Name'],
        ]);

        $response = $this->json('GET', "/core/v1/update-requests/{$updateRequest->id}");

        $response->assertStatus(Response::HTTP_FORBIDDEN);
    }

    public function test_organisation_admin_cannot_view_one()
    {
        $organisation = factory(Organisation::class)->create();
        $user = factory(User::class)->create()->makeOrganisationAdmin($organisation);
        Passport::actingAs($user);

        $serviceLocation = factory(ServiceLocation::class)->create();
        $updateRequest = $serviceLocation->updateRequests()->create([
            'user_id' => factory(User::class)->create()->id,
            'data' => ['name' => 'Test Name'],
        ]);

        $response = $this->json('GET', "/core/v1/update-requests/{$updateRequest->id}");

        $response->assertStatus(Response::HTTP_FORBIDDEN);
    }

    public function test_global_admin_can_view_one()
    {
        $user = factory(User::class)->create()->makeGlobalAdmin();
        Passport::actingAs($user);

        $serviceLocation = factory(ServiceLocation::class)->create();
        $updateRequest = $serviceLocation->updateRequests()->create([
            'user_id' => factory(User::class)->create()->id,
            'data' => ['name' => 'Test Name'],
        ]);

        $response = $this->json('GET', "/core/v1/update-requests/{$updateRequest->id}");

        $response->assertStatus(Response::HTTP_OK);
        $response->assertJsonFragment([
            'id' => $updateRequest->id,
            'user_id' => $updateRequest->user_id,
            'updateable_type' => 'service_locations',
            'updateable_id' => $serviceLocation->id,
            'data' => ['name' => 'Test Name'],
        ]);
    }

    /*
     * Delete a specific update request.
     */

    public function test_guest_cannot_delete_one()
    {
        $serviceLocation = factory(ServiceLocation::class)->create();
        $updateRequest = $serviceLocation->updateRequests()->create([
            'user_id' => factory(User::class)->create()->id,
            'data' => ['name' => 'Test Name'],
        ]);

        $response = $this->json('DELETE', "/core/v1/update-requests/{$updateRequest->id}");

        $response->assertStatus(Response::HTTP_UNAUTHORIZED);
    }

    public function test_service_worker_cannot_delete_one()
    {
        $service = factory(Service::class)->create();
        $user = factory(User::class)->create()->makeServiceWorker($service);
        Passport::actingAs($user);

        $serviceLocation = factory(ServiceLocation::class)->create();
        $updateRequest = $serviceLocation->updateRequests()->create([
            'user_id' => factory(User::class)->create()->id,
            'data' => ['name' => 'Test Name'],
        ]);

        $response = $this->json('DELETE', "/core/v1/update-requests/{$updateRequest->id}");

        $response->assertStatus(Response::HTTP_FORBIDDEN);
    }

    public function test_service_admin_cannot_delete_one()
    {
        $service = factory(Service::class)->create();
        $user = factory(User::class)->create()->makeServiceAdmin($service);
        Passport::actingAs($user);

        $serviceLocation = factory(ServiceLocation::class)->create();
        $updateRequest = $serviceLocation->updateRequests()->create([
            'user_id' => factory(User::class)->create()->id,
            'data' => ['name' => 'Test Name'],
        ]);

        $response = $this->json('DELETE', "/core/v1/update-requests/{$updateRequest->id}");

        $response->assertStatus(Response::HTTP_FORBIDDEN);
    }

    public function test_organisation_admin_cannot_delete_one()
    {
        $organisation = factory(Organisation::class)->create();
        $user = factory(User::class)->create()->makeOrganisationAdmin($organisation);
        Passport::actingAs($user);

        $serviceLocation = factory(ServiceLocation::class)->create();
        $updateRequest = $serviceLocation->updateRequests()->create([
            'user_id' => factory(User::class)->create()->id,
            'data' => ['name' => 'Test Name'],
        ]);

        $response = $this->json('DELETE', "/core/v1/update-requests/{$updateRequest->id}");

        $response->assertStatus(Response::HTTP_FORBIDDEN);
    }

    public function test_global_admin_can_delete_one()
    {
        $user = factory(User::class)->create()->makeGlobalAdmin();
        Passport::actingAs($user);

        $serviceLocation = factory(ServiceLocation::class)->create();
        $updateRequest = $serviceLocation->updateRequests()->create([
            'user_id' => factory(User::class)->create()->id,
            'data' => ['name' => 'Test Name'],
        ]);

        $response = $this->json('DELETE', "/core/v1/update-requests/{$updateRequest->id}");

        $response->assertStatus(Response::HTTP_OK);
        $this->assertSoftDeleted((new UpdateRequest())->getTable(), ['id' => $updateRequest->id]);
    }

    /*
     * Approve a specific update request.
     */
}
