<?php

namespace Tests\Feature;

use App\Events\EndpointHit;
use App\Models\Audit;
use App\Models\File;
use App\Models\Organisation;
use App\Models\Service;
use App\Models\UpdateRequest;
use App\Models\User;
use Carbon\CarbonImmutable;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Event;
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
                'has_logo' => $organisation->hasLogo(),
                'slug' => $organisation->slug,
                'name' => $organisation->name,
                'description' => $organisation->description,
                'url' => $organisation->url,
                'email' => $organisation->email,
                'phone' => $organisation->phone,
                'created_at' => $organisation->created_at->format(CarbonImmutable::ISO8601),
                'updated_at' => $organisation->updated_at->format(CarbonImmutable::ISO8601),
            ],
        ]);
    }

    public function test_audit_created_when_listed()
    {
        $this->fakeEvents();

        $this->json('GET', '/core/v1/organisations');

        Event::assertDispatched(EndpointHit::class, function (EndpointHit $event) {
            return ($event->getAction() === Audit::ACTION_READ);
        });
    }

    public function test_guest_can_sort_by_name()
    {
        $organisationOne = factory(Organisation::class)->create([
            'name' => 'Organisation A',
        ]);
        $organisationTwo = factory(Organisation::class)->create([
            'name' => 'Organisation B',
        ]);

        $response = $this->json('GET', '/core/v1/organisations?sort=-name');
        $data = $this->getResponseContent($response);

        $response->assertStatus(Response::HTTP_OK);
        $this->assertEquals($organisationOne->id, $data['data'][1]['id']);
        $this->assertEquals($organisationTwo->id, $data['data'][0]['id']);
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
            'slug' => 'test-org',
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

    public function test_global_admin_can_create_one_with_single_form_of_contact()
    {
        /**
         * @var \App\Models\User $user
         */
        $user = factory(User::class)->create();
        $user->makeGlobalAdmin();
        $payload = [
            'slug' => 'test-org',
            'name' => 'Test Org',
            'description' => 'Test description',
            'url' => 'http://test-org.example.com',
            'email' => 'info@test-org.example.com',
            'phone' => null,
        ];

        Passport::actingAs($user);

        $response = $this->json('POST', '/core/v1/organisations', $payload);

        $response->assertStatus(Response::HTTP_CREATED);
        $response->assertJsonFragment($payload);
    }

    public function test_global_admin_cannot_create_one_with_no_form_of_contact()
    {
        /**
         * @var \App\Models\User $user
         */
        $user = factory(User::class)->create();
        $user->makeGlobalAdmin();
        $payload = [
            'slug' => 'test-org',
            'name' => 'Test Org',
            'description' => 'Test description',
            'url' => 'http://test-org.example.com',
            'email' => null,
            'phone' => null,
        ];

        Passport::actingAs($user);

        $response = $this->json('POST', '/core/v1/organisations', $payload);

        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
    }

    public function test_global_admin_can_create_one_with_taxonomies()
    {
        /**
         * @var \App\Models\User $user
         */
        $user = factory(User::class)->create();
        $user->makeGlobalAdmin();

        $taxonomy = factory(Taxonomy::class)->states('lga-standards')->create();

        $payload = [
            'slug' => 'test-org',
            'name' => 'Test Org',
            'description' => 'Test description',
            'url' => 'http://test-org.example.com',
            'email' => 'info@test-org.example.com',
            'phone' => null,
            'category_taxonomies' => [$taxonomy->id],
        ];

        Passport::actingAs($user);

        $response = $this->json('POST', '/core/v1/organisations', $payload);

        $response->assertStatus(Response::HTTP_CREATED);

        $organisation = Organisation::findOrFail($response->json('data.id'));
        $this->assertDatabaseHas(table(OrganisationTaxonomy::class), [
            'organisation_id' => $organisation->id,
            'taxonomy_id' => $taxonomy->id,
        ]);
        $this->assertDatabaseHas(table(OrganisationTaxonomy::class), [
            'organisation_id' => $organisation->id,
            'taxonomy_id' => $taxonomy->parent_id,
        ]);

        $responsePayload = $payload;
        $responsePayload['category_taxonomies'] = [
            [
                'id' => $taxonomy->parent->id,
                'parent_id' => $taxonomy->parent->parent_id,
                'name' => $taxonomy->parent->name,
                'created_at' => $taxonomy->parent->created_at->format(CarbonImmutable::ISO8601),
                'updated_at' => $taxonomy->parent->updated_at->format(CarbonImmutable::ISO8601),
            ],
            [
                'id' => $taxonomy->id,
                'parent_id' => $taxonomy->parent_id,
                'name' => $taxonomy->name,
                'created_at' => $taxonomy->created_at->format(CarbonImmutable::ISO8601),
                'updated_at' => $taxonomy->updated_at->format(CarbonImmutable::ISO8601),
            ],
        ];
        $response->assertJsonFragment($responsePayload);
    }

    public function test_audit_created_when_created()
    {
        $this->fakeEvents();

        /**
         * @var \App\Models\User $user
         */
        $user = factory(User::class)->create();
        $user->makeGlobalAdmin();

        Passport::actingAs($user);

        $response = $this->json('POST', '/core/v1/organisations', [
            'slug' => 'test-org',
            'name' => 'Test Org',
            'description' => 'Test description',
            'url' => 'http://test-org.example.com',
            'email' => 'info@test-org.example.com',
            'phone' => '07700000000',
        ]);

        Event::assertDispatched(EndpointHit::class, function (EndpointHit $event) use ($user, $response) {
            return ($event->getAction() === Audit::ACTION_CREATE) &&
                ($event->getUser()->id === $user->id) &&
                ($event->getModel()->id === $this->getResponseContent($response)['data']['id']);
        });
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
                'has_logo' => $organisation->hasLogo(),
                'slug' => $organisation->slug,
                'name' => $organisation->name,
                'description' => $organisation->description,
                'url' => $organisation->url,
                'email' => $organisation->email,
                'phone' => $organisation->phone,
                'created_at' => $organisation->created_at->format(CarbonImmutable::ISO8601),
                'updated_at' => $organisation->updated_at->format(CarbonImmutable::ISO8601),
            ],
        ]);
    }

    public function test_guest_can_view_one_by_slug()
    {
        $organisation = factory(Organisation::class)->create();

        $response = $this->json('GET', "/core/v1/organisations/{$organisation->slug}");

        $response->assertStatus(Response::HTTP_OK);
        $response->assertJsonFragment([
            [
                'id' => $organisation->id,
                'has_logo' => $organisation->hasLogo(),
                'slug' => $organisation->slug,
                'name' => $organisation->name,
                'description' => $organisation->description,
                'url' => $organisation->url,
                'email' => $organisation->email,
                'phone' => $organisation->phone,
                'created_at' => $organisation->created_at->format(CarbonImmutable::ISO8601),
                'updated_at' => $organisation->updated_at->format(CarbonImmutable::ISO8601),
            ],
        ]);
    }

    public function test_audit_created_when_viewed()
    {
        $this->fakeEvents();

        $organisation = factory(Organisation::class)->create();

        $this->json('GET', "/core/v1/organisations/{$organisation->id}");

        Event::assertDispatched(EndpointHit::class, function (EndpointHit $event) use ($organisation) {
            return ($event->getAction() === Audit::ACTION_READ) &&
                ($event->getModel()->id === $organisation->id);
        });
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
            'slug' => 'test-org',
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
            'updateable_type' => UpdateRequest::EXISTING_TYPE_ORGANISATION,
            'updateable_id' => $organisation->id,
        ]);
        $data = UpdateRequest::query()
            ->where('updateable_type', UpdateRequest::EXISTING_TYPE_ORGANISATION)
            ->where('updateable_id', $organisation->id)
            ->firstOrFail()->data;
        $this->assertEquals($data, $payload);
    }

    public function test_organisation_admin_can_update_with_single_form_of_contact()
    {
        $organisation = factory(Organisation::class)->create();
        $user = factory(User::class)->create()->makeOrganisationAdmin($organisation);
        $payload = [
            'slug' => 'test-org',
            'name' => 'Test Org',
            'description' => 'Test description',
            'url' => 'http://test-org.example.com',
            'email' => 'info@test-org.example.com',
            'phone' => null,
        ];

        Passport::actingAs($user);

        $response = $this->json('PUT', "/core/v1/organisations/{$organisation->id}", $payload);

        $response->assertStatus(Response::HTTP_OK);
        $response->assertJsonFragment(['data' => $payload]);
        $this->assertDatabaseHas((new UpdateRequest())->getTable(), [
            'user_id' => $user->id,
            'updateable_type' => UpdateRequest::EXISTING_TYPE_ORGANISATION,
            'updateable_id' => $organisation->id,
        ]);
        $data = UpdateRequest::query()
            ->where('updateable_type', UpdateRequest::EXISTING_TYPE_ORGANISATION)
            ->where('updateable_id', $organisation->id)
            ->firstOrFail()->data;
        $this->assertEquals($data, $payload);
    }

    public function test_organisation_admin_cannot_update_with_no_form_of_contact()
    {
        $organisation = factory(Organisation::class)->create([
            'email' => 'info@test-org.example.com',
            'phone' => null,
        ]);
        $user = factory(User::class)->create()->makeOrganisationAdmin($organisation);

        Passport::actingAs($user);

        $response = $this->json('PUT', "/core/v1/organisations/{$organisation->id}", [
            'slug' => 'test-org',
            'name' => 'Test Org',
            'description' => 'Test description',
            'url' => 'http://test-org.example.com',
            'email' => null,
            'phone' => null,
        ]);

        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
    }

    public function test_only_partial_fields_can_be_updated()
    {
        $organisation = factory(Organisation::class)->create();
        $user = factory(User::class)->create()->makeOrganisationAdmin($organisation);
        $payload = [
            'slug' => 'test-org',
        ];

        Passport::actingAs($user);

        $response = $this->json('PUT', "/core/v1/organisations/{$organisation->id}", $payload);

        $response->assertStatus(Response::HTTP_OK);
        $response->assertJsonFragment(['data' => $payload]);
        $this->assertDatabaseHas((new UpdateRequest())->getTable(), [
            'user_id' => $user->id,
            'updateable_type' => UpdateRequest::EXISTING_TYPE_ORGANISATION,
            'updateable_id' => $organisation->id,
        ]);
        $data = UpdateRequest::query()
            ->where('updateable_type', UpdateRequest::EXISTING_TYPE_ORGANISATION)
            ->where('updateable_id', $organisation->id)
            ->firstOrFail()->data;
        $this->assertEquals($data, $payload);
    }

    public function test_audit_created_when_updated()
    {
        $this->fakeEvents();

        $organisation = factory(Organisation::class)->create();
        $user = factory(User::class)->create()->makeOrganisationAdmin($organisation);

        Passport::actingAs($user);

        $this->json('PUT', "/core/v1/organisations/{$organisation->id}", [
            'slug' => 'test-org',
            'name' => 'Test Org',
            'description' => 'Test description',
            'url' => 'http://test-org.example.com',
            'email' => 'info@test-org.example.com',
            'phone' => '07700000000',
        ]);

        Event::assertDispatched(EndpointHit::class, function (EndpointHit $event) use ($user, $organisation) {
            return ($event->getAction() === Audit::ACTION_UPDATE) &&
                ($event->getUser()->id === $user->id) &&
                ($event->getModel()->id === $organisation->id);
        });
    }

    public function test_fields_removed_for_existing_update_requests()
    {
        $organisation = factory(Organisation::class)->create();
        $user = factory(User::class)->create()->makeSuperAdmin();

        Passport::actingAs($user);

        $responseOne = $this->json('PUT', "/core/v1/organisations/{$organisation->id}", [
            'name' => 'Random 1',
        ]);
        $responseOne->assertStatus(Response::HTTP_OK);

        $responseTwo = $this->json('PUT', "/core/v1/organisations/{$organisation->id}", [
            'name' => 'Random 2',
            'slug' => 'random-1',
        ]);
        $responseTwo->assertStatus(Response::HTTP_OK);

        $updateRequestOne = UpdateRequest::withTrashed()->findOrFail($this->getResponseContent($responseOne)['id']);
        $updateRequestTwo = UpdateRequest::findOrFail($this->getResponseContent($responseTwo)['id']);

        $this->assertArrayNotHasKey('name', $updateRequestOne->data);
        $this->assertArrayHasKey('name', $updateRequestTwo->data);
        $this->assertArrayHasKey('slug', $updateRequestTwo->data);
        $this->assertSoftDeleted($updateRequestOne->getTable(), ['id' => $updateRequestOne->id]);
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

    public function test_audit_created_when_deleted()
    {
        $this->fakeEvents();

        $organisation = factory(Organisation::class)->create();
        $user = factory(User::class)->create()->makeSuperAdmin();

        Passport::actingAs($user);

        $this->json('DELETE', "/core/v1/organisations/{$organisation->id}");

        Event::assertDispatched(EndpointHit::class, function (EndpointHit $event) use ($user, $organisation) {
            return ($event->getAction() === Audit::ACTION_DELETE) &&
                ($event->getUser()->id === $user->id) &&
                ($event->getModel()->id === $organisation->id);
        });
    }

    /*
     * Get a specific organisation's logo.
     */

    public function test_guest_can_view_logo()
    {
        $organisation = factory(Organisation::class)->create();

        $response = $this->get("/core/v1/organisations/{$organisation->id}/logo.png");

        $response->assertStatus(Response::HTTP_OK);
        $response->assertHeader('Content-Type', 'image/png');
    }

    public function test_audit_created_when_logo_viewed()
    {
        $this->fakeEvents();

        $organisation = factory(Organisation::class)->create();

        $this->get("/core/v1/organisations/{$organisation->id}/logo.png");

        Event::assertDispatched(EndpointHit::class, function (EndpointHit $event) use ($organisation) {
            return ($event->getAction() === Audit::ACTION_READ) &&
                ($event->getModel()->id === $organisation->id);
        });
    }

    /*
     * Upload a specific organisation's logo.
     */


    public function test_organisation_admin_can_upload_logo()
    {
        /**
         * @var \App\Models\User $user
         */
        $user = factory(User::class)->create();
        $user->makeGlobalAdmin();
        $image = Storage::disk('local')->get('/test-data/image.png');

        Passport::actingAs($user);

        $imageResponse = $this->json('POST', '/core/v1/files', [
            'is_private' => false,
            'mime_type' => 'image/png',
            'file' => 'data:image/png;base64,' . base64_encode($image),
        ]);

        $response = $this->json('POST', '/core/v1/organisations', [
            'slug' => 'test-org',
            'name' => 'Test Org',
            'description' => 'Test description',
            'url' => 'http://test-org.example.com',
            'email' => 'info@test-org.example.com',
            'phone' => '07700000000',
            'logo_file_id' => $this->getResponseContent($imageResponse, 'data.id'),
        ]);
        $organisationId = $this->getResponseContent($response, 'data.id');

        $response->assertStatus(Response::HTTP_CREATED);
        $this->assertDatabaseHas(table(Organisation::class), [
            'id' => $organisationId,
        ]);
        $this->assertDatabaseMissing(table(Organisation::class), [
            'id' => $organisationId,
            'logo_file_id' => null,
        ]);
    }

    /*
     * Delete a specific organisation's logo.
     */

    public function test_organisation_admin_can_delete_logo()
    {
        /**
         * @var \App\Models\User $user
         */
        $user = factory(User::class)->create();
        $user->makeGlobalAdmin();
        $organisation = factory(Organisation::class)->create([
            'logo_file_id' => factory(File::class)->create()->id,
        ]);
        $payload = [
            'slug' => $organisation->slug,
            'name' => $organisation->name,
            'description' => $organisation->description,
            'url' => $organisation->url,
            'email' => $organisation->email,
            'phone' => $organisation->phone,
            'logo_file_id' => null,
        ];

        Passport::actingAs($user);

        $response = $this->json('PUT', "/core/v1/organisations/{$organisation->id}", $payload);

        $response->assertStatus(Response::HTTP_OK);
        $this->assertDatabaseHas(table(UpdateRequest::class), ['updateable_id' => $organisation->id]);
        $updateRequest = UpdateRequest::where('updateable_id', $organisation->id)->firstOrFail();
        $this->assertEquals(null, $updateRequest->data['logo_file_id']);
    }
}
