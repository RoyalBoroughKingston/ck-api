<?php

namespace Tests\Feature;

use App\Models\File;
use App\Models\Organisation;
use App\Models\Service;
use App\Models\UpdateRequest;
use App\Models\User;
use Illuminate\Http\Response;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Storage;
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

    /*
     * Delete a specific organisation.
     */

    public function test_guest_cannot_delete_one()
    {
        $organisation = factory(Organisation::class)->create();

        $response = $this->json('DELETE', "/core/v1/organisations/{$organisation->id}");

        $response->assertStatus(Response::HTTP_UNAUTHORIZED);
    }

    public function test_service_worker_cannot_delete_one()
    {
        $service = factory(Service::class)->create();
        $user = factory(User::class)->create()->makeServiceWorker($service);
        $organisation = factory(Organisation::class)->create();

        Passport::actingAs($user);

        $response = $this->json('DELETE', "/core/v1/organisations/{$organisation->id}");

        $response->assertStatus(Response::HTTP_FORBIDDEN);
    }

    public function test_service_admin_cannot_delete_one()
    {
        $service = factory(Service::class)->create();
        $user = factory(User::class)->create()->makeServiceAdmin($service);
        $organisation = factory(Organisation::class)->create();

        Passport::actingAs($user);

        $response = $this->json('DELETE', "/core/v1/organisations/{$organisation->id}");

        $response->assertStatus(Response::HTTP_FORBIDDEN);
    }

    public function test_organisation_admin_cannot_delete_one()
    {
        $organisation = factory(Organisation::class)->create();
        $user = factory(User::class)->create()->makeOrganisationAdmin($organisation);

        Passport::actingAs($user);

        $response = $this->json('DELETE', "/core/v1/organisations/{$organisation->id}");

        $response->assertStatus(Response::HTTP_FORBIDDEN);
    }

    public function test_global_admin_cannot_delete_one()
    {
        $organisation = factory(Organisation::class)->create();
        $user = factory(User::class)->create()->makeGlobalAdmin();

        Passport::actingAs($user);

        $response = $this->json('DELETE', "/core/v1/organisations/{$organisation->id}");

        $response->assertStatus(Response::HTTP_FORBIDDEN);
    }

    public function test_super_admin_can_delete_one()
    {
        $organisation = factory(Organisation::class)->create();
        $user = factory(User::class)->create()->makeSuperAdmin();

        Passport::actingAs($user);

        $response = $this->json('DELETE', "/core/v1/organisations/{$organisation->id}");

        $response->assertStatus(Response::HTTP_OK);
        $this->assertDatabaseMissing((new Organisation())->getTable(), ['id' => $organisation->id]);
    }

    /*
     * Get a specific organisation's logo.
     */

    public function test_guest_can_view_logo()
    {
        $organisation = factory(Organisation::class)->create();

        $response = $this->get("/core/v1/organisations/{$organisation->id}/logo");

        $response->assertStatus(Response::HTTP_OK);
        $response->assertHeader('Content-Type', 'image/png');
    }

    /*
     * Upload a specific organisation's logo.
     */

    public function test_guest_cannot_upload_logo()
    {
        $organisation = factory(Organisation::class)->create();

        $response = $this->json('POST', "/core/v1/organisations/{$organisation->id}/logo");

        $response->assertStatus(Response::HTTP_UNAUTHORIZED);
    }

    public function test_service_worker_cannot_upload_logo()
    {
        $organisation = factory(Organisation::class)->create();
        $service = factory(Service::class)->create(['organisation_id' => $organisation->id]);
        $user = factory(User::class)->create();
        $user->makeServiceWorker($service);

        Passport::actingAs($user);

        $response = $this->json('POST', "/core/v1/organisations/{$organisation->id}/logo");

        $response->assertStatus(Response::HTTP_FORBIDDEN);
    }

    public function test_service_admin_cannot_upload_logo()
    {
        $organisation = factory(Organisation::class)->create();
        $service = factory(Service::class)->create(['organisation_id' => $organisation->id]);
        $user = factory(User::class)->create();
        $user->makeServiceAdmin($service);

        Passport::actingAs($user);

        $response = $this->json('POST', "/core/v1/organisations/{$organisation->id}/logo");

        $response->assertStatus(Response::HTTP_FORBIDDEN);
    }

    public function test_organisation_admin_can_upload_logo()
    {
        $organisation = factory(Organisation::class)->create();
        $user = factory(User::class)->create();
        $user->makeOrganisationAdmin($organisation);
        $image = Storage::disk('local')->get('/test-data/image.png');

        Passport::actingAs($user);

        $response = $this->json('POST', "/core/v1/organisations/{$organisation->id}/logo", [
            'file' => 'data:image/png;base64,' . base64_encode($image),
        ]);

        $response->assertStatus(Response::HTTP_CREATED);
        $response->assertJsonFragment(['message' => 'The update request has been received and needs to be reviewed']);
        $this->assertDatabaseHas((new UpdateRequest())->getTable(), [
            'user_id' => $user->id,
            'updateable_type' => 'organisations',
            'updateable_id' => $organisation->id,
        ]);
    }

    /*
     * Delete a specific organisation's logo.
     */

    public function test_guest_cannot_delete_logo()
    {
        $organisation = factory(Organisation::class)->create();

        $response = $this->json('DELETE', "/core/v1/organisations/{$organisation->id}/logo");

        $response->assertStatus(Response::HTTP_UNAUTHORIZED);
    }

    public function test_service_worker_cannot_delete_logo()
    {
        $organisation = factory(Organisation::class)->create();
        $service = factory(Service::class)->create(['organisation_id' => $organisation->id]);
        $user = factory(User::class)->create();
        $user->makeServiceWorker($service);

        Passport::actingAs($user);

        $response = $this->json('DELETE', "/core/v1/organisations/{$organisation->id}/logo");

        $response->assertStatus(Response::HTTP_FORBIDDEN);
    }

    public function test_service_admin_cannot_delete_logo()
    {
        $organisation = factory(Organisation::class)->create();
        $service = factory(Service::class)->create(['organisation_id' => $organisation->id]);
        $user = factory(User::class)->create();
        $user->makeServiceAdmin($service);

        Passport::actingAs($user);

        $response = $this->json('DELETE', "/core/v1/organisations/{$organisation->id}/logo");

        $response->assertStatus(Response::HTTP_FORBIDDEN);
    }

    public function test_organisation_admin_can_delete_logo()
    {
        $file = File::create(['filename' => 'test/png', 'mime_type' => 'image/png']);
        $organisation = factory(Organisation::class)->create(['logo_file_id' => $file->id]);
        $user = factory(User::class)->create();
        $user->makeOrganisationAdmin($organisation);

        Passport::actingAs($user);

        $response = $this->json('DELETE', "/core/v1/organisations/{$organisation->id}/logo");

        $response->assertStatus(Response::HTTP_OK);
        $response->assertJsonFragment(['message' => 'The update request has been received and needs to be reviewed']);
        $this->assertDatabaseHas((new UpdateRequest())->getTable(), [
            'user_id' => $user->id,
            'updateable_type' => 'organisations',
            'updateable_id' => $organisation->id,
        ]);
    }
}