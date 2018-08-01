<?php

namespace Tests\Feature;

use App\Models\Collection;
use App\Models\CollectionTaxonomy;
use App\Models\Organisation;
use App\Models\Service;
use App\Models\Taxonomy;
use App\Models\User;
use Illuminate\Http\Response;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Storage;
use Laravel\Passport\Passport;
use Tests\TestCase;

class CollectionPersonasTest extends TestCase
{
    /*
     * List all the persona collections.
     */

    public function test_guest_can_list_them()
    {
        $response = $this->json('GET', '/core/v1/collections/personas');

        $response->assertStatus(Response::HTTP_OK);
        $response->assertJsonCollection([
            'id',
            'name',
            'intro',
            'subtitle',
            'order',
            'category_taxonomies' => [
                '*' => [
                    'id',
                    'parent_id',
                    'name',
                    'created_at',
                    'updated_at',
                ],
            ],
            'created_at',
            'updated_at',
        ]);
    }

    /*
     * Create a collection persona.
     */

    public function test_guest_cannot_create_one()
    {
        $response = $this->json('POST', '/core/v1/collections/personas');

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

        $response = $this->json('POST', '/core/v1/collections/personas');

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

        $response = $this->json('POST', '/core/v1/collections/personas');

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

        $response = $this->json('POST', '/core/v1/collections/personas');

        $response->assertStatus(Response::HTTP_FORBIDDEN);
    }

    public function test_global_admin_cannot_create_one()
    {
        /**
         * @var \App\Models\User $user
         */
        $user = factory(User::class)->create();
        $user->makeGlobalAdmin();

        Passport::actingAs($user);

        $response = $this->json('POST', '/core/v1/collections/personas');

        $response->assertStatus(Response::HTTP_FORBIDDEN);
    }

    public function test_super_admin_can_create_one()
    {
        /**
         * @var \App\Models\User $user
         */
        $user = factory(User::class)->create();
        $user->makeSuperAdmin();
        $randomCategory = Taxonomy::category()->children()->inRandomOrder()->firstOrFail();

        Passport::actingAs($user);

        $response = $this->json('POST', '/core/v1/collections/personas', [
            'name' => 'Test Persona',
            'intro' => 'Lorem ipsum',
            'subtitle' => 'Subtitle here',
            'order' => 1,
            'category_taxonomies' => [$randomCategory->id],
        ]);

        $response->assertStatus(Response::HTTP_CREATED);
        $response->assertJsonResource([
            'id',
            'name',
            'intro',
            'subtitle',
            'order',
            'category_taxonomies' => [
                '*' => [
                    'id',
                    'parent_id',
                    'name',
                    'created_at',
                    'updated_at',
                ],
            ],
            'created_at',
            'updated_at',
        ]);
        $response->assertJsonFragment([
            'name' => 'Test Persona',
            'intro' => 'Lorem ipsum',
            'subtitle' => 'Subtitle here',
            'order' => 1,
        ]);
        $response->assertJsonFragment([
            'id' => $randomCategory->id,
        ]);
    }

    public function test_order_is_updated_when_created_at_beginning()
    {
        // Delete the existing seeded personas.
        $this->truncateCollectionPersonas();

        /**
         * @var \App\Models\User $user
         */
        $user = factory(User::class)->create();
        $user->makeSuperAdmin();
        $first = Collection::create([
            'type' => Collection::TYPE_PERSONA,
            'name' => 'First',
            'order' => 1,
            'meta' => [
                'intro' => 'Lorem ipsum',
                'subtitle' => 'Subtitle here',
            ],
        ]);
        $second = Collection::create([
            'type' => Collection::TYPE_PERSONA,
            'name' => 'Second',
            'order' => 2,
            'meta' => [
                'intro' => 'Lorem ipsum',
                'subtitle' => 'Subtitle here',
            ],
        ]);
        $third = Collection::create([
            'type' => Collection::TYPE_PERSONA,
            'name' => 'Third',
            'order' => 3,
            'meta' => [
                'intro' => 'Lorem ipsum',
                'subtitle' => 'Subtitle here',
            ],
        ]);

        Passport::actingAs($user);

        $response = $this->json('POST', '/core/v1/collections/personas', [
            'name' => 'Test Persona',
            'intro' => 'Lorem ipsum',
            'subtitle' => 'Subtitle here',
            'order' => 1,
            'category_taxonomies' => [],
        ]);

        $response->assertStatus(Response::HTTP_CREATED);
        $this->assertDatabaseHas((new Collection())->getTable(), ['order' => 1]);
        $this->assertDatabaseHas((new Collection())->getTable(), ['id' => $first->id, 'order' => 2]);
        $this->assertDatabaseHas((new Collection())->getTable(), ['id' => $second->id, 'order' => 3]);
        $this->assertDatabaseHas((new Collection())->getTable(), ['id' => $third->id, 'order' => 4]);
    }

    public function test_order_is_updated_when_created_at_middle()
    {
        // Delete the existing seeded personas.
        $this->truncateCollectionPersonas();

        /**
         * @var \App\Models\User $user
         */
        $user = factory(User::class)->create();
        $user->makeSuperAdmin();
        $first = Collection::create([
            'type' => Collection::TYPE_PERSONA,
            'name' => 'First',
            'order' => 1,
            'meta' => [
                'intro' => 'Lorem ipsum',
                'subtitle' => 'Subtitle here',
            ],
        ]);
        $second = Collection::create([
            'type' => Collection::TYPE_PERSONA,
            'name' => 'Second',
            'order' => 2,
            'meta' => [
                'intro' => 'Lorem ipsum',
                'subtitle' => 'Subtitle here',
            ],
        ]);
        $third = Collection::create([
            'type' => Collection::TYPE_PERSONA,
            'name' => 'Third',
            'order' => 3,
            'meta' => [
                'intro' => 'Lorem ipsum',
                'subtitle' => 'Subtitle here',
            ],
        ]);

        Passport::actingAs($user);

        $response = $this->json('POST', '/core/v1/collections/personas', [
            'name' => 'Test Persona',
            'intro' => 'Lorem ipsum',
            'subtitle' => 'Subtitle here',
            'order' => 2,
            'category_taxonomies' => [],
        ]);

        $response->assertStatus(Response::HTTP_CREATED);
        $this->assertDatabaseHas((new Collection())->getTable(), ['id' => $first->id, 'order' => 1]);
        $this->assertDatabaseHas((new Collection())->getTable(), ['order' => 2]);
        $this->assertDatabaseHas((new Collection())->getTable(), ['id' => $second->id, 'order' => 3]);
        $this->assertDatabaseHas((new Collection())->getTable(), ['id' => $third->id, 'order' => 4]);
    }

    public function test_order_is_updated_when_created_at_end()
    {
        // Delete the existing seeded personas.
        $this->truncateCollectionPersonas();

        /**
         * @var \App\Models\User $user
         */
        $user = factory(User::class)->create();
        $user->makeSuperAdmin();
        $first = Collection::create([
            'type' => Collection::TYPE_PERSONA,
            'name' => 'First',
            'order' => 1,
            'meta' => [
                'intro' => 'Lorem ipsum',
                'subtitle' => 'Subtitle here',
            ],
        ]);
        $second = Collection::create([
            'type' => Collection::TYPE_PERSONA,
            'name' => 'Second',
            'order' => 2,
            'meta' => [
                'intro' => 'Lorem ipsum',
                'subtitle' => 'Subtitle here',
            ],
        ]);
        $third = Collection::create([
            'type' => Collection::TYPE_PERSONA,
            'name' => 'Third',
            'order' => 3,
            'meta' => [
                'intro' => 'Lorem ipsum',
                'subtitle' => 'Subtitle here',
            ],
        ]);

        Passport::actingAs($user);

        $response = $this->json('POST', '/core/v1/collections/personas', [
            'name' => 'Test Persona',
            'intro' => 'Lorem ipsum',
            'subtitle' => 'Subtitle here',
            'order' => 4,
            'category_taxonomies' => [],
        ]);

        $response->assertStatus(Response::HTTP_CREATED);
        $this->assertDatabaseHas((new Collection())->getTable(), ['id' => $first->id, 'order' => 1]);
        $this->assertDatabaseHas((new Collection())->getTable(), ['id' => $second->id, 'order' => 2]);
        $this->assertDatabaseHas((new Collection())->getTable(), ['id' => $third->id, 'order' => 3]);
        $this->assertDatabaseHas((new Collection())->getTable(), ['order' => 4]);
    }

    public function test_order_cannot_be_less_than_1_when_created()
    {
        // Delete the existing seeded personas.
        $this->truncateCollectionPersonas();

        /**
         * @var \App\Models\User $user
         */
        $user = factory(User::class)->create();
        $user->makeSuperAdmin();

        Passport::actingAs($user);

        $response = $this->json('POST', '/core/v1/collections/personas', [
            'name' => 'Test Persona',
            'intro' => 'Lorem ipsum',
            'subtitle' => 'Subtitle here',
            'order' => 0,
            'category_taxonomies' => [],
        ]);

        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
    }

    public function test_order_cannot_be_greater_than_count_plus_1_when_created()
    {
        // Delete the existing seeded personas.
        $this->truncateCollectionPersonas();

        /**
         * @var \App\Models\User $user
         */
        $user = factory(User::class)->create();
        $user->makeSuperAdmin();
        Collection::create([
            'type' => Collection::TYPE_PERSONA,
            'name' => 'First',
            'order' => 1,
            'meta' => [
                'intro' => 'Lorem ipsum',
                'subtitle' => 'Subtitle here',
            ],
        ]);
        Collection::create([
            'type' => Collection::TYPE_PERSONA,
            'name' => 'Second',
            'order' => 2,
            'meta' => [
                'intro' => 'Lorem ipsum',
                'subtitle' => 'Subtitle here',
            ],
        ]);

        Passport::actingAs($user);

        $response = $this->json('POST', '/core/v1/collections/personas', [
            'name' => 'Test Persona',
            'intro' => 'Lorem ipsum',
            'subtitle' => 'Subtitle here',
            'order' => 4,
            'category_taxonomies' => [],
        ]);

        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
    }

    /*
     * Get a specific persona collection.
     */

    public function test_guest_can_view_one()
    {
        $persona = Collection::personas()->inRandomOrder()->firstOrFail();

        $response = $this->json('GET', "/core/v1/collections/personas/{$persona->id}");

        $response->assertStatus(Response::HTTP_OK);
        $response->assertJsonResource([
            'id',
            'name',
            'intro',
            'subtitle',
            'order',
            'category_taxonomies' => [
                '*' => [
                    'id',
                    'parent_id',
                    'name',
                    'created_at',
                    'updated_at',
                ],
            ],
            'created_at',
            'updated_at',
        ]);
        $response->assertJsonFragment([
            'id' => $persona->id,
            'name' => $persona->name,
            'intro' => $persona->meta['intro'],
            'subtitle' => $persona->meta['subtitle'],
            'order' => $persona->order,
            'created_at' => $persona->created_at->format(Carbon::ISO8601),
            'updated_at' => $persona->updated_at->format(Carbon::ISO8601),
        ]);
    }

    /*
     * Update a specific persona collection.
     */

    public function test_guest_cannot_update_one()
    {
        $persona = Collection::personas()->inRandomOrder()->firstOrFail();

        $response = $this->json('PUT', "/core/v1/collections/personas/{$persona->id}");

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
        $persona = Collection::personas()->inRandomOrder()->firstOrFail();

        Passport::actingAs($user);

        $response = $this->json('PUT', "/core/v1/collections/personas/{$persona->id}");

        $response->assertStatus(Response::HTTP_FORBIDDEN);
    }

    public function test_service_admin_cannot_update_one()
    {
        /**
         * @var \App\Models\Service $service
         * @var \App\Models\User $user
         */
        $service = factory(Service::class)->create();
        $user = factory(User::class)->create();
        $user->makeServiceAdmin($service);
        $persona = Collection::personas()->inRandomOrder()->firstOrFail();

        Passport::actingAs($user);

        $response = $this->json('PUT', "/core/v1/collections/personas/{$persona->id}");

        $response->assertStatus(Response::HTTP_FORBIDDEN);
    }

    public function test_organisation_admin_cannot_update_one()
    {
        /**
         * @var \App\Models\Organisation $organisation
         * @var \App\Models\User $user
         */
        $organisation = factory(Organisation::class)->create();
        $user = factory(User::class)->create();
        $user->makeOrganisationAdmin($organisation);
        $persona = Collection::personas()->inRandomOrder()->firstOrFail();

        Passport::actingAs($user);

        $response = $this->json('PUT', "/core/v1/collections/personas/{$persona->id}");

        $response->assertStatus(Response::HTTP_FORBIDDEN);
    }

    public function test_global_admin_cannot_update_one()
    {
        /**
         * @var \App\Models\User $user
         */
        $user = factory(User::class)->create();
        $user->makeGlobalAdmin();
        $persona = Collection::personas()->inRandomOrder()->firstOrFail();

        Passport::actingAs($user);

        $response = $this->json('PUT', "/core/v1/collections/personas/{$persona->id}");

        $response->assertStatus(Response::HTTP_FORBIDDEN);
    }

    public function test_super_admin_can_update_one()
    {
        /**
         * @var \App\Models\User $user
         */
        $user = factory(User::class)->create();
        $user->makeSuperAdmin();
        $persona = Collection::personas()->inRandomOrder()->firstOrFail();
        $taxonomy = Taxonomy::category()->children()->inRandomOrder()->firstOrFail();

        Passport::actingAs($user);

        $response = $this->json('PUT', "/core/v1/collections/personas/{$persona->id}", [
            'name' => 'Test Persona',
            'intro' => 'Lorem ipsum',
            'subtitle' => 'Subtitle here',
            'order' => 1,
            'category_taxonomies' => [$taxonomy->id],
        ]);

        $response->assertStatus(Response::HTTP_OK);
        $response->assertJsonResource([
            'id',
            'name',
            'intro',
            'subtitle',
            'order',
            'category_taxonomies' => [
                '*' => [
                    'id',
                    'parent_id',
                    'name',
                    'created_at',
                    'updated_at',
                ],
            ],
            'created_at',
            'updated_at',
        ]);
        $response->assertJsonFragment([
            'name' => 'Test Persona',
            'intro' => 'Lorem ipsum',
            'subtitle' => 'Subtitle here',
            'order' => 1,
        ]);
        $response->assertJsonFragment([
            'id' => $taxonomy->id,
        ]);
    }

    public function test_order_is_updated_when_updated_to_beginning()
    {
        // Delete the existing seeded personas.
        $this->truncateCollectionPersonas();

        /**
         * @var \App\Models\User $user
         */
        $user = factory(User::class)->create();
        $user->makeSuperAdmin();
        $first = Collection::create([
            'type' => Collection::TYPE_PERSONA,
            'name' => 'First',
            'order' => 1,
            'meta' => [
                'intro' => 'Lorem ipsum',
                'subtitle' => 'Subtitle here',
            ],
        ]);
        $second = Collection::create([
            'type' => Collection::TYPE_PERSONA,
            'name' => 'Second',
            'order' => 2,
            'meta' => [
                'intro' => 'Lorem ipsum',
                'subtitle' => 'Subtitle here',
            ],
        ]);
        $third = Collection::create([
            'type' => Collection::TYPE_PERSONA,
            'name' => 'Third',
            'order' => 3,
            'meta' => [
                'intro' => 'Lorem ipsum',
                'subtitle' => 'Subtitle here',
            ],
        ]);

        Passport::actingAs($user);

        $response = $this->json('PUT', "/core/v1/collections/personas/{$third->id}", [
            'name' => 'Third',
            'intro' => 'Lorem ipsum',
            'subtitle' => 'Subtitle here',
            'order' => 1,
            'category_taxonomies' => [],
        ]);

        $response->assertStatus(Response::HTTP_OK);
        $this->assertDatabaseHas((new Collection())->getTable(), ['id' => $first->id, 'order' => 2]);
        $this->assertDatabaseHas((new Collection())->getTable(), ['id' => $second->id, 'order' => 3]);
        $this->assertDatabaseHas((new Collection())->getTable(), ['id' => $third->id, 'order' => 1]);
    }

    public function test_order_is_updated_when_updated_to_middle()
    {
        // Delete the existing seeded personas.
        $this->truncateCollectionPersonas();

        /**
         * @var \App\Models\User $user
         */
        $user = factory(User::class)->create();
        $user->makeSuperAdmin();
        $first = Collection::create([
            'type' => Collection::TYPE_PERSONA,
            'name' => 'First',
            'order' => 1,
            'meta' => [
                'intro' => 'Lorem ipsum',
                'subtitle' => 'Subtitle here',
            ],
        ]);
        $second = Collection::create([
            'type' => Collection::TYPE_PERSONA,
            'name' => 'Second',
            'order' => 2,
            'meta' => [
                'intro' => 'Lorem ipsum',
                'subtitle' => 'Subtitle here',
            ],
        ]);
        $third = Collection::create([
            'type' => Collection::TYPE_PERSONA,
            'name' => 'Third',
            'order' => 3,
            'meta' => [
                'intro' => 'Lorem ipsum',
                'subtitle' => 'Subtitle here',
            ],
        ]);

        Passport::actingAs($user);

        $response = $this->json('PUT', "/core/v1/collections/personas/{$first->id}", [
            'name' => 'First',
            'intro' => 'Lorem ipsum',
            'subtitle' => 'Subtitle here',
            'order' => 2,
            'category_taxonomies' => [],
        ]);

        $response->assertStatus(Response::HTTP_OK);
        $this->assertDatabaseHas((new Collection())->getTable(), ['id' => $first->id, 'order' => 2]);
        $this->assertDatabaseHas((new Collection())->getTable(), ['id' => $second->id, 'order' => 1]);
        $this->assertDatabaseHas((new Collection())->getTable(), ['id' => $third->id, 'order' => 3]);
    }

    public function test_order_is_updated_when_updated_to_end()
    {
        // Delete the existing seeded personas.
        $this->truncateCollectionPersonas();

        /**
         * @var \App\Models\User $user
         */
        $user = factory(User::class)->create();
        $user->makeSuperAdmin();
        $first = Collection::create([
            'type' => Collection::TYPE_PERSONA,
            'name' => 'First',
            'order' => 1,
            'meta' => [
                'intro' => 'Lorem ipsum',
                'subtitle' => 'Subtitle here',
            ],
        ]);
        $second = Collection::create([
            'type' => Collection::TYPE_PERSONA,
            'name' => 'Second',
            'order' => 2,
            'meta' => [
                'intro' => 'Lorem ipsum',
                'subtitle' => 'Subtitle here',
            ],
        ]);
        $third = Collection::create([
            'type' => Collection::TYPE_PERSONA,
            'name' => 'Third',
            'order' => 3,
            'meta' => [
                'intro' => 'Lorem ipsum',
                'subtitle' => 'Subtitle here',
            ],
        ]);

        Passport::actingAs($user);

        $response = $this->json('PUT', "/core/v1/collections/personas/{$first->id}", [
            'name' => 'First',
            'intro' => 'Lorem ipsum',
            'subtitle' => 'Subtitle here',
            'order' => 3,
            'category_taxonomies' => [],
        ]);

        $response->assertStatus(Response::HTTP_OK);
        $this->assertDatabaseHas((new Collection())->getTable(), ['id' => $first->id, 'order' => 3]);
        $this->assertDatabaseHas((new Collection())->getTable(), ['id' => $second->id, 'order' => 1]);
        $this->assertDatabaseHas((new Collection())->getTable(), ['id' => $third->id, 'order' => 2]);
    }

    public function test_order_cannot_be_less_than_1_when_updated()
    {
        // Delete the existing seeded personas.
        $this->truncateCollectionPersonas();

        /**
         * @var \App\Models\User $user
         */
        $user = factory(User::class)->create();
        $user->makeSuperAdmin();
        $persona = Collection::create([
            'type' => Collection::TYPE_PERSONA,
            'name' => 'First',
            'order' => 1,
            'meta' => [
                'intro' => 'Lorem ipsum',
                'subtitle' => 'Subtitle here',
            ],
        ]);

        Passport::actingAs($user);

        $response = $this->json('PUT', "/core/v1/collections/personas/{$persona->id}", [
            'name' => 'First',
            'intro' => 'Lorem ipsum',
            'subtitle' => 'Subtitle here',
            'order' => 0,
            'category_taxonomies' => [],
        ]);

        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
    }

    public function test_order_cannot_be_greater_than_count_plus_1_when_updated()
    {
        // Delete the existing seeded personas.
        $this->truncateCollectionPersonas();

        /**
         * @var \App\Models\User $user
         */
        $user = factory(User::class)->create();
        $user->makeSuperAdmin();
        $persona = Collection::create([
            'type' => Collection::TYPE_PERSONA,
            'name' => 'First',
            'order' => 1,
            'meta' => [
                'intro' => 'Lorem ipsum',
                'subtitle' => 'Subtitle here',
            ],
        ]);

        Passport::actingAs($user);

        $response = $this->json('PUT', "/core/v1/collections/personas/{$persona->id}", [
            'name' => 'First',
            'intro' => 'Lorem ipsum',
            'subtitle' => 'Subtitle here',
            'order' => 2,
            'category_taxonomies' => [],
        ]);

        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
    }

    /*
     * Delete a specific persona collection.
     */

    public function test_guest_cannot_delete_one()
    {
        $persona = Collection::personas()->inRandomOrder()->firstOrFail();

        $response = $this->json('DELETE', "/core/v1/collections/personas/{$persona->id}");

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
        $persona = Collection::personas()->inRandomOrder()->firstOrFail();

        Passport::actingAs($user);

        $response = $this->json('DELETE', "/core/v1/collections/personas/{$persona->id}");

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
        $persona = Collection::personas()->inRandomOrder()->firstOrFail();

        Passport::actingAs($user);

        $response = $this->json('DELETE', "/core/v1/collections/personas/{$persona->id}");

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
        $persona = Collection::personas()->inRandomOrder()->firstOrFail();

        Passport::actingAs($user);

        $response = $this->json('DELETE', "/core/v1/collections/personas/{$persona->id}");

        $response->assertStatus(Response::HTTP_FORBIDDEN);
    }

    public function test_global_admin_cannot_delete_one()
    {
        /**
         * @var \App\Models\User $user
         */
        $user = factory(User::class)->create();
        $user->makeGlobalAdmin();
        $persona = Collection::personas()->inRandomOrder()->firstOrFail();

        Passport::actingAs($user);

        $response = $this->json('DELETE', "/core/v1/collections/personas/{$persona->id}");

        $response->assertStatus(Response::HTTP_FORBIDDEN);
    }

    public function test_super_admin_can_delete_one()
    {
        /**
         * @var \App\Models\User $user
         */
        $user = factory(User::class)->create();
        $user->makeSuperAdmin();
        $persona = Collection::personas()->inRandomOrder()->firstOrFail();

        Passport::actingAs($user);

        $response = $this->json('DELETE', "/core/v1/collections/personas/{$persona->id}");

        $response->assertStatus(Response::HTTP_OK);
        $this->assertDatabaseMissing((new Collection())->getTable(), ['id' => $persona->id]);
        $this->assertDatabaseMissing((new CollectionTaxonomy())->getTable(), ['collection_id' => $persona->id]);
    }

    public function test_order_is_updated_when_deleted_at_beginning()
    {
        // Delete the existing seeded personas.
        $this->truncateCollectionPersonas();

        /**
         * @var \App\Models\User $user
         */
        $user = factory(User::class)->create();
        $user->makeSuperAdmin();
        $first = Collection::create([
            'type' => Collection::TYPE_PERSONA,
            'name' => 'First',
            'order' => 1,
            'meta' => [
                'intro' => 'Lorem ipsum',
                'subtitle' => 'Subtitle here',
            ],
        ]);
        $second = Collection::create([
            'type' => Collection::TYPE_PERSONA,
            'name' => 'Second',
            'order' => 2,
            'meta' => [
                'intro' => 'Lorem ipsum',
                'subtitle' => 'Subtitle here',
            ],
        ]);
        $third = Collection::create([
            'type' => Collection::TYPE_PERSONA,
            'name' => 'Third',
            'order' => 3,
            'meta' => [
                'intro' => 'Lorem ipsum',
                'subtitle' => 'Subtitle here',
            ],
        ]);

        Passport::actingAs($user);

        $response = $this->json('DELETE', "/core/v1/collections/personas/{$first->id}");

        $response->assertStatus(Response::HTTP_OK);
        $this->assertDatabaseMissing((new Collection())->getTable(), ['id' => $first->id]);
        $this->assertDatabaseHas((new Collection())->getTable(), ['id' => $second->id, 'order' => 1]);
        $this->assertDatabaseHas((new Collection())->getTable(), ['id' => $third->id, 'order' => 2]);
    }

    public function test_order_is_updated_when_deleted_at_middle()
    {
        // Delete the existing seeded personas.
        $this->truncateCollectionPersonas();

        /**
         * @var \App\Models\User $user
         */
        $user = factory(User::class)->create();
        $user->makeSuperAdmin();
        $first = Collection::create([
            'type' => Collection::TYPE_PERSONA,
            'name' => 'First',
            'order' => 1,
            'meta' => [
                'intro' => 'Lorem ipsum',
                'subtitle' => 'Subtitle here',
            ],
        ]);
        $second = Collection::create([
            'type' => Collection::TYPE_PERSONA,
            'name' => 'Second',
            'order' => 2,
            'meta' => [
                'intro' => 'Lorem ipsum',
                'subtitle' => 'Subtitle here',
            ],
        ]);
        $third = Collection::create([
            'type' => Collection::TYPE_PERSONA,
            'name' => 'Third',
            'order' => 3,
            'meta' => [
                'intro' => 'Lorem ipsum',
                'subtitle' => 'Subtitle here',
            ],
        ]);

        Passport::actingAs($user);

        $response = $this->json('DELETE', "/core/v1/collections/personas/{$second->id}");

        $response->assertStatus(Response::HTTP_OK);
        $this->assertDatabaseMissing((new Collection())->getTable(), ['id' => $second->id]);
        $this->assertDatabaseHas((new Collection())->getTable(), ['id' => $first->id, 'order' => 1]);
        $this->assertDatabaseHas((new Collection())->getTable(), ['id' => $third->id, 'order' => 2]);
    }

    public function test_order_is_updated_when_deleted_at_end()
    {
        // Delete the existing seeded personas.
        $this->truncateCollectionPersonas();

        /**
         * @var \App\Models\User $user
         */
        $user = factory(User::class)->create();
        $user->makeSuperAdmin();
        $first = Collection::create([
            'type' => Collection::TYPE_PERSONA,
            'name' => 'First',
            'order' => 1,
            'meta' => [
                'intro' => 'Lorem ipsum',
                'subtitle' => 'Subtitle here',
            ],
        ]);
        $second = Collection::create([
            'type' => Collection::TYPE_PERSONA,
            'name' => 'Second',
            'order' => 2,
            'meta' => [
                'intro' => 'Lorem ipsum',
                'subtitle' => 'Subtitle here',
            ],
        ]);
        $third = Collection::create([
            'type' => Collection::TYPE_PERSONA,
            'name' => 'Third',
            'order' => 3,
            'meta' => [
                'intro' => 'Lorem ipsum',
                'subtitle' => 'Subtitle here',
            ],
        ]);

        Passport::actingAs($user);

        $response = $this->json('DELETE', "/core/v1/collections/personas/{$third->id}");

        $response->assertStatus(Response::HTTP_OK);
        $this->assertDatabaseMissing((new Collection())->getTable(), ['id' => $third->id]);
        $this->assertDatabaseHas((new Collection())->getTable(), ['id' => $first->id, 'order' => 1]);
        $this->assertDatabaseHas((new Collection())->getTable(), ['id' => $second->id, 'order' => 2]);
    }

    /*
     * Get a specific persona collection's image.
     */

    public function test_guest_can_view_image()
    {
        $persona = Collection::personas()->inRandomOrder()->firstOrFail();

        $response = $this->get("/core/v1/collections/personas/{$persona->id}/image");

        $response->assertStatus(Response::HTTP_OK);
        $response->assertHeader('Content-Type', 'image/png');
    }

    /*
     * Upload a specific persona collection's image.
     */

    public function test_guest_cannot_upload_image()
    {
        $persona = Collection::personas()->inRandomOrder()->firstOrFail();

        $response = $this->json('POST', "/core/v1/collections/personas/{$persona->id}/image");

        $response->assertStatus(Response::HTTP_UNAUTHORIZED);
    }

    public function test_service_worker_cannot_upload_image()
    {
        /**
         * @var \App\Models\Service $service
         * @var \App\Models\User $user
         */
        $service = factory(Service::class)->create();
        $user = factory(User::class)->create();
        $user->makeServiceWorker($service);
        $persona = Collection::personas()->inRandomOrder()->firstOrFail();

        Passport::actingAs($user);

        $response = $this->json('POST', "/core/v1/collections/personas/{$persona->id}/image");

        $response->assertStatus(Response::HTTP_FORBIDDEN);
    }

    public function test_service_admin_cannot_upload_image()
    {
        /**
         * @var \App\Models\Service $service
         * @var \App\Models\User $user
         */
        $service = factory(Service::class)->create();
        $user = factory(User::class)->create();
        $user->makeServiceAdmin($service);
        $persona = Collection::personas()->inRandomOrder()->firstOrFail();

        Passport::actingAs($user);

        $response = $this->json('POST', "/core/v1/collections/personas/{$persona->id}/image");

        $response->assertStatus(Response::HTTP_FORBIDDEN);
    }

    public function test_organisation_admin_cannot_upload_image()
    {
        /**
         * @var \App\Models\Organisation $organisation
         * @var \App\Models\User $user
         */
        $organisation = factory(Organisation::class)->create();
        $user = factory(User::class)->create();
        $user->makeOrganisationAdmin($organisation);
        $persona = Collection::personas()->inRandomOrder()->firstOrFail();

        Passport::actingAs($user);

        $response = $this->json('POST', "/core/v1/collections/personas/{$persona->id}/image");

        $response->assertStatus(Response::HTTP_FORBIDDEN);
    }

    public function test_global_admin_cannot_upload_image()
    {
        /**
         * @var \App\Models\User $user
         */
        $user = factory(User::class)->create();
        $user->makeGlobalAdmin();
        $persona = Collection::personas()->inRandomOrder()->firstOrFail();

        Passport::actingAs($user);

        $response = $this->json('POST', "/core/v1/collections/personas/{$persona->id}/image");

        $response->assertStatus(Response::HTTP_FORBIDDEN);
    }

    public function test_super_admin_can_upload_image()
    {
        /**
         * @var \App\Models\User $user
         */
        $user = factory(User::class)->create();
        $user->makeSuperAdmin();
        $persona = Collection::personas()->inRandomOrder()->firstOrFail();
        $image = Storage::disk('local')->get('/test-data/image.png');

        Passport::actingAs($user);

        $response = $this->json('POST', "/core/v1/collections/personas/{$persona->id}/image", [
            'file' => 'data:image/png;base64,' . base64_encode($image),
        ]);
        $content = $this->get("/core/v1/collections/personas/{$persona->id}/image")->content();

        $response->assertStatus(Response::HTTP_CREATED);
        $this->assertEquals($image, $content);
    }

    /*
     * Delete a specific persona collection's image.
     */

    public function test_guest_cannot_delete_image()
    {
        $persona = Collection::personas()->inRandomOrder()->firstOrFail();

        $response = $this->json('DELETE', "/core/v1/collections/personas/{$persona->id}/image");

        $response->assertStatus(Response::HTTP_UNAUTHORIZED);
    }

    public function test_service_worker_cannot_delete_image()
    {
        /**
         * @var \App\Models\Service $service
         * @var \App\Models\User $user
         */
        $service = factory(Service::class)->create();
        $user = factory(User::class)->create();
        $user->makeServiceWorker($service);
        $persona = Collection::personas()->inRandomOrder()->firstOrFail();

        Passport::actingAs($user);

        $response = $this->json('DELETE', "/core/v1/collections/personas/{$persona->id}/image");

        $response->assertStatus(Response::HTTP_FORBIDDEN);
    }

    public function test_service_admin_cannot_delete_image()
    {
        /**
         * @var \App\Models\Service $service
         * @var \App\Models\User $user
         */
        $service = factory(Service::class)->create();
        $user = factory(User::class)->create();
        $user->makeServiceAdmin($service);
        $persona = Collection::personas()->inRandomOrder()->firstOrFail();

        Passport::actingAs($user);

        $response = $this->json('DELETE', "/core/v1/collections/personas/{$persona->id}/image");

        $response->assertStatus(Response::HTTP_FORBIDDEN);
    }

    public function test_organisation_admin_cannot_delete_image()
    {
        /**
         * @var \App\Models\Organisation $organisation
         * @var \App\Models\User $user
         */
        $organisation = factory(Organisation::class)->create();
        $user = factory(User::class)->create();
        $user->makeOrganisationAdmin($organisation);
        $persona = Collection::personas()->inRandomOrder()->firstOrFail();

        Passport::actingAs($user);

        $response = $this->json('DELETE', "/core/v1/collections/personas/{$persona->id}/image");

        $response->assertStatus(Response::HTTP_FORBIDDEN);
    }

    public function test_global_admin_cannot_delete_image()
    {
        /**
         * @var \App\Models\User $user
         */
        $user = factory(User::class)->create();
        $user->makeGlobalAdmin();
        $persona = Collection::personas()->inRandomOrder()->firstOrFail();

        Passport::actingAs($user);

        $response = $this->json('DELETE', "/core/v1/collections/personas/{$persona->id}/image");

        $response->assertStatus(Response::HTTP_FORBIDDEN);
    }

    public function test_super_admin_can_delete_image()
    {
        /**
         * @var \App\Models\User $user
         */
        $user = factory(User::class)->create();
        $user->makeSuperAdmin();
        $persona = Collection::personas()->inRandomOrder()->firstOrFail();
        $image = Storage::disk('local')->get('/test-data/image.png');

        Passport::actingAs($user);

        $this->json('POST', "/core/v1/collections/personas/{$persona->id}/image", [
            'file' => 'data:image/png;base64,' . base64_encode($image),
        ]);

        $response = $this->json('DELETE', "/core/v1/collections/personas/{$persona->id}/image");

        $response->assertStatus(Response::HTTP_OK);
    }
}
