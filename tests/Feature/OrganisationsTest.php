<?php

namespace Tests\Feature;

use App\Models\Organisation;
use App\Models\Service;
use App\Models\UpdateRequest;
use App\Models\User;
use Illuminate\Http\Response;
use Illuminate\Support\Carbon;
use Laravel\Passport\Passport;
use Tests\TestCase;

class OrganisationsTest extends TestCase
{
    /*
     * List all the organisations.
     */

    public function test_guest_can_list_them()
    {
        $organisation = factory(Organisation::class)->create();

        $response = $this->json('GET', '/core/v1/organisations');

        $response->assertStatus(Response::HTTP_OK);
        $response->assertJsonFragment([
            [
                'id' => $organisation->id,
                'name' => $organisation->name,
                'description' => $organisation->description,
                'url' => $organisation->url,
                'email' => $organisation->email,
                'phone' => $organisation->phone,
                'created_at' => $organisation->created_at->format(Carbon::ISO8601),
                'updated_at' => $organisation->updated_at->format(Carbon::ISO8601),
            ]
        ]);
    }

    /*
     * Create an organisation.
     */

    public function test_guest_cannot_create_one()
    {
        $response = $this->json('POST', '/core/v1/organisations');

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

        $response = $this->json('POST', '/core/v1/organisations');

        $response->assertStatus(Response::HTTP_FORBIDDEN);
    }

    public function test_service_admin_cannot_create_one()
    {
        /**
         * @var \App\Models\Service $service
         * @var \App\Models\User $user
         */
        $service = factory(Service::class)->create();
        $user = factory(User::class)->create();
        $user->makeServiceAdmin($service);

        Passport::actingAs($user);

        $response = $this->json('POST', '/core/v1/organisations');

        $response->assertStatus(Response::HTTP_FORBIDDEN);
    }

    public function test_organisation_admin_cannot_create_one()
    {
        /**
         * @var \App\Models\Organisation $organisation
         * @var \App\Models\User $user
         */
        $organisation = factory(Organisation::class)->create();
        $user = factory(User::class)->create();
        $user->makeOrganisationAdmin($organisation);

        Passport::actingAs($user);

        $response = $this->json('POST', '/core/v1/organisations');

        $response->assertStatus(Response::HTTP_FORBIDDEN);
    }

    public function test_global_admin_can_create_one()
    {
        /**
         * @var \App\Models\User $user
         */
        $user = factory(User::class)->create();
        $user->makeGlobalAdmin();
        $payload = [
            'name' => 'Test Org',
            'description' => 'Test description',
            'url' => 'http://test-org.example.com',
            'email' => 'info@test-org.example.com',
            'phone' => '07700000000',
        ];

        Passport::actingAs($user);

        $response = $this->json('POST', '/core/v1/organisations', $payload);

        $response->assertStatus(Response::HTTP_CREATED);
        $response->assertJsonFragment($payload);
    }

    /*
     * Get a specific organisation.
     */

    public function test_guest_can_view_one()
    {
        $organisation = factory(Organisation::class)->create();

        $response = $this->json('GET', "/core/v1/organisations/{$organisation->id}");

        $response->assertStatus(Response::HTTP_OK);
        $response->assertJsonFragment([
            [
                'id' => $organisation->id,
                'name' => $organisation->name,
                'description' => $organisation->description,
                'url' => $organisation->url,
                'email' => $organisation->email,
                'phone' => $organisation->phone,
                'created_at' => $organisation->created_at->format(Carbon::ISO8601),
                'updated_at' => $organisation->updated_at->format(Carbon::ISO8601),
            ]
        ]);
    }
    
    /*
     * Update a specific organisation.
     */

    public function test_guest_cannot_update_one()
    {
        $organisation = factory(Organisation::class)->create();

        $response = $this->json('PUT', "/core/v1/organisations/{$organisation->id}");

        $response->assertStatus(Response::HTTP_UNAUTHORIZED);
    }

    public function test_service_worker_cannot_update_one()
    {
        $service = factory(Service::class)->create();
        $user = factory(User::class)->create()->makeServiceWorker($service);
        $organisation = factory(Organisation::class)->create();

        Passport::actingAs($user);

        $response = $this->json('PUT', "/core/v1/organisations/{$organisation->id}");

        $response->assertStatus(Response::HTTP_FORBIDDEN);
    }

    public function test_service_admin_cannot_update_one()
    {
        $service = factory(Service::class)->create();
        $user = factory(User::class)->create()->makeServiceAdmin($service);
        $organisation = factory(Organisation::class)->create();

        Passport::actingAs($user);

        $response = $this->json('PUT', "/core/v1/organisations/{$organisation->id}");

        $response->assertStatus(Response::HTTP_FORBIDDEN);
    }

    public function test_organisation_admin_can_update_one()
    {
        $organisation = factory(Organisation::class)->create();
        $user = factory(User::class)->create()->makeOrganisationAdmin($organisation);
        $payload = [
            'name' => 'Test Org',
            'description' => 'Test description',
            'url' => 'http://test-org.example.com',
            'email' => 'info@test-org.example.com',
            'phone' => '07700000000',
        ];

        Passport::actingAs($user);

        $response = $this->json('PUT', "/core/v1/organisations/{$organisation->id}", $payload);

        $response->assertStatus(Response::HTTP_OK);
        $response->assertJsonFragment(['data' => $payload]);
        $this->assertDatabaseHas((new UpdateRequest())->getTable(), [
            'user_id' => $user->id,
            'updateable_type' => 'organisations',
            'updateable_id' => $organisation->id,
        ]);
        $data = UpdateRequest::query()
            ->where('updateable_type', 'organisations')
            ->where('updateable_id', $organisation->id)
            ->firstOrFail()->data;
        $this->assertEquals($data, $payload);
    }
}
