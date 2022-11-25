<?php

namespace Tests\Feature;

use App\Events\EndpointHit;
use App\Models\Audit;
use App\Models\Collection;
use App\Models\CollectionTaxonomy;
use App\Models\File;
use App\Models\Organisation;
use App\Models\Service;
use App\Models\Taxonomy;
use App\Models\User;
use Carbon\CarbonImmutable;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Event;
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
            'slug',
            'name',
            'intro',
            'subtitle',
            'order',
            'homepage',
            'sideboxes' => [
                '*' => [
                    'title',
                    'content',
                ],
            ],
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

    public function test_audit_created_when_listed()
    {
        $this->fakeEvents();

        $this->json('GET', '/core/v1/collections/personas');

        Event::assertDispatched(EndpointHit::class, function (EndpointHit $event) {
            return ($event->getAction() === Audit::ACTION_READ);
        });
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
            'homepage' => false,
            'sideboxes' => [],
            'category_taxonomies' => [$randomCategory->id],
        ]);

        $response->assertStatus(Response::HTTP_CREATED);
        $response->assertJsonResource([
            'id',
            'slug',
            'name',
            'intro',
            'subtitle',
            'order',
            'homepage',
            'sideboxes' => [
                '*' => [
                    'title',
                    'content',
                ],
            ],
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
            'homepage' => false,
            'sideboxes' => [],
        ]);
        $response->assertJsonFragment([
            'id' => $randomCategory->id,
        ]);
    }

    public function test_super_admin_can_create_a_homepage_one()
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
            'homepage' => true,
            'sideboxes' => [],
            'category_taxonomies' => [$randomCategory->id],
        ]);

        $response->assertStatus(Response::HTTP_CREATED);
        $response->assertJsonResource([
            'id',
            'name',
            'intro',
            'subtitle',
            'order',
            'homepage',
            'sideboxes' => [
                '*' => [
                    'title',
                    'content',
                ],
            ],
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
            'homepage' => true,
            'sideboxes' => [],
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
            'slug' => 'first',
            'name' => 'First',
            'order' => 1,
            'homepage' => false,
            'meta' => [
                'intro' => 'Lorem ipsum',
                'subtitle' => 'Subtitle here',
                'sideboxes' => [],
            ],
        ]);
        $second = Collection::create([
            'type' => Collection::TYPE_PERSONA,
            'slug' => 'second',
            'name' => 'Second',
            'order' => 2,
            'homepage' => false,
            'meta' => [
                'intro' => 'Lorem ipsum',
                'subtitle' => 'Subtitle here',
                'sideboxes' => [],
            ],
        ]);
        $third = Collection::create([
            'type' => Collection::TYPE_PERSONA,
            'slug' => 'third',
            'name' => 'Third',
            'order' => 3,
            'homepage' => false,
            'meta' => [
                'intro' => 'Lorem ipsum',
                'subtitle' => 'Subtitle here',
                'sideboxes' => [],
            ],
        ]);

        Passport::actingAs($user);

        $response = $this->json('POST', '/core/v1/collections/personas', [
            'name' => 'Test Persona',
            'intro' => 'Lorem ipsum',
            'subtitle' => 'Subtitle here',
            'order' => 1,
            'homepage' => false,
            'sideboxes' => [],
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
            'slug' => 'first',
            'name' => 'First',
            'order' => 1,
            'homepage' => false,
            'meta' => [
                'intro' => 'Lorem ipsum',
                'subtitle' => 'Subtitle here',
                'sideboxes' => [],
            ],
        ]);
        $second = Collection::create([
            'type' => Collection::TYPE_PERSONA,
            'slug' => 'second',
            'name' => 'Second',
            'order' => 2,
            'homepage' => false,
            'meta' => [
                'intro' => 'Lorem ipsum',
                'subtitle' => 'Subtitle here',
                'sideboxes' => [],
            ],
        ]);
        $third = Collection::create([
            'type' => Collection::TYPE_PERSONA,
            'slug' => 'third',
            'name' => 'Third',
            'order' => 3,
            'homepage' => false,
            'meta' => [
                'intro' => 'Lorem ipsum',
                'subtitle' => 'Subtitle here',
                'sideboxes' => [],
            ],
        ]);

        Passport::actingAs($user);

        $response = $this->json('POST', '/core/v1/collections/personas', [
            'name' => 'Test Persona',
            'intro' => 'Lorem ipsum',
            'subtitle' => 'Subtitle here',
            'order' => 2,
            'homepage' => false,
            'sideboxes' => [],
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
            'slug' => 'first',
            'name' => 'First',
            'order' => 1,
            'homepage' => false,
            'meta' => [
                'intro' => 'Lorem ipsum',
                'subtitle' => 'Subtitle here',
                'sideboxes' => [],
            ],
        ]);
        $second = Collection::create([
            'type' => Collection::TYPE_PERSONA,
            'slug' => 'second',
            'name' => 'Second',
            'order' => 2,
            'homepage' => false,
            'meta' => [
                'intro' => 'Lorem ipsum',
                'subtitle' => 'Subtitle here',
                'sideboxes' => [],
            ],
        ]);
        $third = Collection::create([
            'type' => Collection::TYPE_PERSONA,
            'slug' => 'third',
            'name' => 'Third',
            'order' => 3,
            'homepage' => false,
            'meta' => [
                'intro' => 'Lorem ipsum',
                'subtitle' => 'Subtitle here',
                'sideboxes' => [],
            ],
        ]);

        Passport::actingAs($user);

        $response = $this->json('POST', '/core/v1/collections/personas', [
            'name' => 'Test Persona',
            'intro' => 'Lorem ipsum',
            'subtitle' => 'Subtitle here',
            'order' => 4,
            'homepage' => false,
            'sideboxes' => [],
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
            'sideboxes' => [],
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
            'slug' => 'first',
            'name' => 'First',
            'order' => 1,
            'homepage' => false,
            'meta' => [
                'intro' => 'Lorem ipsum',
                'subtitle' => 'Subtitle here',
                'sideboxes' => [],
            ],
        ]);
        Collection::create([
            'type' => Collection::TYPE_PERSONA,
            'slug' => 'second',
            'name' => 'Second',
            'order' => 2,
            'homepage' => false,
            'meta' => [
                'intro' => 'Lorem ipsum',
                'subtitle' => 'Subtitle here',
                'sideboxes' => [],
            ],
        ]);

        Passport::actingAs($user);

        $response = $this->json('POST', '/core/v1/collections/personas', [
            'name' => 'Test Persona',
            'intro' => 'Lorem ipsum',
            'subtitle' => 'Subtitle here',
            'order' => 4,
            'sideboxes' => [],
            'category_taxonomies' => [],
        ]);

        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
    }

    public function test_audit_created_when_created()
    {
        $this->fakeEvents();

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
            'homepage' => false,
            'sideboxes' => [],
            'category_taxonomies' => [$randomCategory->id],
        ]);

        Event::assertDispatched(EndpointHit::class, function (EndpointHit $event) use ($user, $response) {
            return ($event->getAction() === Audit::ACTION_CREATE) &&
                ($event->getUser()->id === $user->id) &&
                ($event->getModel()->id === $this->getResponseContent($response)['data']['id']);
        });
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
            'slug',
            'name',
            'intro',
            'subtitle',
            'order',
            'homepage',
            'sideboxes' => [
                '*' => [
                    'title',
                    'content',
                ],
            ],
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
            'slug' => $persona->slug,
            'name' => $persona->name,
            'intro' => $persona->meta['intro'],
            'subtitle' => $persona->meta['subtitle'],
            'order' => $persona->order,
            'sideboxes' => $persona->meta['sideboxes'],
            'created_at' => $persona->created_at->format(CarbonImmutable::ISO8601),
            'updated_at' => $persona->updated_at->format(CarbonImmutable::ISO8601),
        ]);
    }

    public function test_guest_can_view_one_by_slug()
    {
        $persona = Collection::personas()->inRandomOrder()->firstOrFail();

        $response = $this->json('GET', "/core/v1/collections/personas/{$persona->slug}");

        $response->assertStatus(Response::HTTP_OK);
        $response->assertJsonResource([
            'id',
            'slug',
            'name',
            'intro',
            'subtitle',
            'order',
            'sideboxes' => [
                '*' => [
                    'title',
                    'content',
                ],
            ],
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
            'slug' => $persona->slug,
            'name' => $persona->name,
            'intro' => $persona->meta['intro'],
            'subtitle' => $persona->meta['subtitle'],
            'order' => $persona->order,
            'homepage' => $persona->homepage,
            'sideboxes' => $persona->meta['sideboxes'],
            'created_at' => $persona->created_at->format(CarbonImmutable::ISO8601),
            'updated_at' => $persona->updated_at->format(CarbonImmutable::ISO8601),
        ]);
    }

    public function test_audit_created_when_viewed()
    {
        $this->fakeEvents();

        $persona = Collection::personas()->inRandomOrder()->firstOrFail();

        $this->json('GET', "/core/v1/collections/personas/{$persona->id}");

        Event::assertDispatched(EndpointHit::class, function (EndpointHit $event) use ($persona) {
            return ($event->getAction() === Audit::ACTION_READ) &&
                ($event->getModel()->id === $persona->id);
        });
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

    public function test_global_admin_can_update_one()
    {
        /**
         * @var \App\Models\User $user
         */
        $user = factory(User::class)->create();
        $user->makeGlobalAdmin();
        $persona = Collection::personas()->inRandomOrder()->firstOrFail();
        $taxonomy = Taxonomy::category()->children()->inRandomOrder()->firstOrFail();

        Passport::actingAs($user);

        $response = $this->json('PUT', "/core/v1/collections/personas/{$persona->id}", [
            'slug' => 'test-persona',
            'name' => 'Test Persona',
            'intro' => 'Lorem ipsum',
            'subtitle' => 'Subtitle here',
            'order' => 1,
            'homepage' => true,
            'sideboxes' => [],
            'category_taxonomies' => [$taxonomy->id],
        ]);

        $response->assertStatus(Response::HTTP_OK);
        $response->assertJsonResource([
            'id',
            'name',
            'intro',
            'subtitle',
            'order',
            'homepage',
            'sideboxes' => [
                '*' => [
                    'title',
                    'content',
                ],
            ],
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
            'homepage' => true,
            'sideboxes' => [],
        ]);
        $response->assertJsonFragment([
            'id' => $taxonomy->id,
        ]);
    }

    public function test_global_admin_can_update_homepage()
    {
        /**
         * @var \App\Models\User $user
         */
        $user = factory(User::class)->create();
        $user->makeGlobalAdmin();
        $persona = Collection::personas()->inRandomOrder()->firstOrFail();
        $taxonomy = Taxonomy::category()->children()->inRandomOrder()->firstOrFail();

        Passport::actingAs($user);

        $response = $this->json('PUT', "/core/v1/collections/personas/{$persona->id}", [
            'name' => 'Test Persona',
            'intro' => 'Lorem ipsum',
            'subtitle' => 'Subtitle here',
            'order' => 1,
            'homepage' => false,
            'sideboxes' => [],
            'category_taxonomies' => [$taxonomy->id],
        ]);

        $response->assertStatus(Response::HTTP_OK);
        $response->assertJsonResource([
            'id',
            'slug',
            'name',
            'intro',
            'subtitle',
            'order',
            'homepage',
            'sideboxes' => [
                '*' => [
                    'title',
                    'content',
                ],
            ],
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
            'slug' => 'test-persona',
            'name' => 'Test Persona',
            'intro' => 'Lorem ipsum',
            'subtitle' => 'Subtitle here',
            'order' => 1,
            'homepage' => false,
            'sideboxes' => [],
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
            'slug' => 'first',
            'name' => 'First',
            'order' => 1,
            'homepage' => false,
            'meta' => [
                'intro' => 'Lorem ipsum',
                'subtitle' => 'Subtitle here',
                'image_file_id' => null,
                'sideboxes' => [],
            ],
        ]);
        $second = Collection::create([
            'type' => Collection::TYPE_PERSONA,
            'slug' => 'second',
            'name' => 'Second',
            'order' => 2,
            'homepage' => false,
            'meta' => [
                'intro' => 'Lorem ipsum',
                'subtitle' => 'Subtitle here',
                'image_file_id' => null,
                'sideboxes' => [],
            ],
        ]);
        $third = Collection::create([
            'type' => Collection::TYPE_PERSONA,
            'slug' => 'third',
            'name' => 'Third',
            'order' => 3,
            'homepage' => false,
            'meta' => [
                'intro' => 'Lorem ipsum',
                'subtitle' => 'Subtitle here',
                'image_file_id' => null,
                'sideboxes' => [],
            ],
        ]);

        Passport::actingAs($user);

        $response = $this->json('PUT', "/core/v1/collections/personas/{$third->id}", [
            'slug' => 'third',
            'name' => 'Third',
            'intro' => 'Lorem ipsum',
            'subtitle' => 'Subtitle here',
            'order' => 1,
            'homepage' => false,
            'sideboxes' => [],
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
            'slug' => 'first',
            'name' => 'First',
            'order' => 1,
            'homepage' => false,
            'meta' => [
                'intro' => 'Lorem ipsum',
                'subtitle' => 'Subtitle here',
                'image_file_id' => null,
                'sideboxes' => [],
            ],
        ]);
        $second = Collection::create([
            'type' => Collection::TYPE_PERSONA,
            'slug' => 'second',
            'name' => 'Second',
            'order' => 2,
            'homepage' => false,
            'meta' => [
                'intro' => 'Lorem ipsum',
                'subtitle' => 'Subtitle here',
                'image_file_id' => null,
                'sideboxes' => [],
            ],
        ]);
        $third = Collection::create([
            'type' => Collection::TYPE_PERSONA,
            'slug' => 'third',
            'name' => 'Third',
            'order' => 3,
            'homepage' => false,
            'meta' => [
                'intro' => 'Lorem ipsum',
                'subtitle' => 'Subtitle here',
                'image_file_id' => null,
                'sideboxes' => [],
            ],
        ]);

        Passport::actingAs($user);

        $response = $this->json('PUT', "/core/v1/collections/personas/{$first->id}", [
            'slug' => 'first',
            'name' => 'First',
            'intro' => 'Lorem ipsum',
            'subtitle' => 'Subtitle here',
            'order' => 2,
            'homepage' => false,
            'sideboxes' => [],
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
            'slug' => 'first',
            'name' => 'First',
            'order' => 1,
            'homepage' => false,
            'meta' => [
                'intro' => 'Lorem ipsum',
                'subtitle' => 'Subtitle here',
                'image_file_id' => null,
                'sideboxes' => [],
            ],
        ]);
        $second = Collection::create([
            'type' => Collection::TYPE_PERSONA,
            'slug' => 'second',
            'name' => 'Second',
            'order' => 2,
            'homepage' => false,
            'meta' => [
                'intro' => 'Lorem ipsum',
                'subtitle' => 'Subtitle here',
                'image_file_id' => null,
                'sideboxes' => [],
            ],
        ]);
        $third = Collection::create([
            'type' => Collection::TYPE_PERSONA,
            'slug' => 'third',
            'name' => 'Third',
            'order' => 3,
            'homepage' => false,
            'meta' => [
                'intro' => 'Lorem ipsum',
                'subtitle' => 'Subtitle here',
                'image_file_id' => null,
                'sideboxes' => [],
            ],
        ]);

        Passport::actingAs($user);

        $response = $this->json('PUT', "/core/v1/collections/personas/{$first->id}", [
            'slug' => 'first',
            'name' => 'First',
            'intro' => 'Lorem ipsum',
            'subtitle' => 'Subtitle here',
            'order' => 3,
            'homepage' => false,
            'sideboxes' => [],
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
            'slug' => 'first',
            'name' => 'First',
            'order' => 1,
            'homepage' => false,
            'meta' => [
                'intro' => 'Lorem ipsum',
                'subtitle' => 'Subtitle here',
                'image_file_id' => null,
                'sideboxes' => [],
            ],
        ]);

        Passport::actingAs($user);

        $response = $this->json('PUT', "/core/v1/collections/personas/{$persona->id}", [
            'slug' => 'first',
            'name' => 'First',
            'intro' => 'Lorem ipsum',
            'subtitle' => 'Subtitle here',
            'order' => 0,
            'homepage' => false,
            'sideboxes' => [],
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
            'slug' => 'first',
            'name' => 'First',
            'order' => 1,
            'homepage' => false,
            'meta' => [
                'intro' => 'Lorem ipsum',
                'subtitle' => 'Subtitle here',
                'image_file_id' => null,
                'sideboxes' => [],
            ],
        ]);

        Passport::actingAs($user);

        $response = $this->json('PUT', "/core/v1/collections/personas/{$persona->id}", [
            'slug' => 'first',
            'name' => 'First',
            'intro' => 'Lorem ipsum',
            'subtitle' => 'Subtitle here',
            'order' => 2,
            'homepage' => false,
            'sideboxes' => [],
            'category_taxonomies' => [],
        ]);

        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
    }

    public function test_audit_created_when_updated()
    {
        $this->fakeEvents();

        /**
         * @var \App\Models\User $user
         */
        $user = factory(User::class)->create();
        $user->makeSuperAdmin();
        $persona = Collection::personas()->inRandomOrder()->firstOrFail();
        $taxonomy = Taxonomy::category()->children()->inRandomOrder()->firstOrFail();

        Passport::actingAs($user);

        $this->json('PUT', "/core/v1/collections/personas/{$persona->id}", [
            'slug' => 'test-persona',
            'name' => 'Test Persona',
            'intro' => 'Lorem ipsum',
            'subtitle' => 'Subtitle here',
            'order' => 1,
            'homepage' => false,
            'sideboxes' => [],
            'category_taxonomies' => [$taxonomy->id],
        ]);

        Event::assertDispatched(EndpointHit::class, function (EndpointHit $event) use ($user, $persona) {
            return ($event->getAction() === Audit::ACTION_UPDATE) &&
                ($event->getUser()->id === $user->id) &&
                ($event->getModel()->id === $persona->id);
        });
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
            'slug' => 'first',
            'name' => 'First',
            'order' => 1,
            'homepage' => false,
            'meta' => [
                'intro' => 'Lorem ipsum',
                'subtitle' => 'Subtitle here',
                'sideboxes' => [],
            ],
        ]);
        $second = Collection::create([
            'type' => Collection::TYPE_PERSONA,
            'slug' => 'second',
            'name' => 'Second',
            'order' => 2,
            'homepage' => false,
            'meta' => [
                'intro' => 'Lorem ipsum',
                'subtitle' => 'Subtitle here',
                'sideboxes' => [],
            ],
        ]);
        $third = Collection::create([
            'type' => Collection::TYPE_PERSONA,
            'slug' => 'third',
            'name' => 'Third',
            'order' => 3,
            'homepage' => false,
            'meta' => [
                'intro' => 'Lorem ipsum',
                'subtitle' => 'Subtitle here',
                'sideboxes' => [],
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
            'slug' => 'first',
            'name' => 'First',
            'order' => 1,
            'homepage' => false,
            'meta' => [
                'intro' => 'Lorem ipsum',
                'subtitle' => 'Subtitle here',
                'sideboxes' => [],
            ],
        ]);
        $second = Collection::create([
            'type' => Collection::TYPE_PERSONA,
            'slug' => 'second',
            'name' => 'Second',
            'order' => 2,
            'homepage' => false,
            'meta' => [
                'intro' => 'Lorem ipsum',
                'subtitle' => 'Subtitle here',
                'sideboxes' => [],
            ],
        ]);
        $third = Collection::create([
            'type' => Collection::TYPE_PERSONA,
            'slug' => 'third',
            'name' => 'Third',
            'order' => 3,
            'homepage' => false,
            'meta' => [
                'intro' => 'Lorem ipsum',
                'subtitle' => 'Subtitle here',
                'sideboxes' => [],
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
            'slug' => 'first',
            'name' => 'First',
            'order' => 1,
            'homepage' => false,
            'meta' => [
                'intro' => 'Lorem ipsum',
                'subtitle' => 'Subtitle here',
                'sideboxes' => [],
            ],
        ]);
        $second = Collection::create([
            'type' => Collection::TYPE_PERSONA,
            'slug' => 'second',
            'name' => 'Second',
            'order' => 2,
            'homepage' => false,
            'meta' => [
                'intro' => 'Lorem ipsum',
                'subtitle' => 'Subtitle here',
                'sideboxes' => [],
            ],
        ]);
        $third = Collection::create([
            'type' => Collection::TYPE_PERSONA,
            'slug' => 'third',
            'name' => 'Third',
            'order' => 3,
            'homepage' => false,
            'meta' => [
                'intro' => 'Lorem ipsum',
                'subtitle' => 'Subtitle here',
                'sideboxes' => [],
            ],
        ]);

        Passport::actingAs($user);

        $response = $this->json('DELETE', "/core/v1/collections/personas/{$third->id}");

        $response->assertStatus(Response::HTTP_OK);
        $this->assertDatabaseMissing((new Collection())->getTable(), ['id' => $third->id]);
        $this->assertDatabaseHas((new Collection())->getTable(), ['id' => $first->id, 'order' => 1]);
        $this->assertDatabaseHas((new Collection())->getTable(), ['id' => $second->id, 'order' => 2]);
    }

    public function test_audit_created_when_deleted()
    {
        $this->fakeEvents();

        /**
         * @var \App\Models\User $user
         */
        $user = factory(User::class)->create();
        $user->makeSuperAdmin();
        $persona = Collection::personas()->inRandomOrder()->firstOrFail();

        Passport::actingAs($user);

        $this->json('DELETE', "/core/v1/collections/personas/{$persona->id}");

        Event::assertDispatched(EndpointHit::class, function (EndpointHit $event) use ($user, $persona) {
            return ($event->getAction() === Audit::ACTION_DELETE) &&
                ($event->getUser()->id === $user->id) &&
                ($event->getModel()->id === $persona->id);
        });
    }

    /*
     * Get a specific persona collection's image.
     */

    public function test_guest_can_view_image()
    {
        $persona = Collection::personas()->inRandomOrder()->firstOrFail();

        $response = $this->get("/core/v1/collections/personas/{$persona->id}/image.png");

        $response->assertStatus(Response::HTTP_OK);
        $response->assertHeader('Content-Type', 'image/png');
    }

    public function test_audit_created_when_image_viewed()
    {
        $this->fakeEvents();

        $persona = Collection::personas()->inRandomOrder()->firstOrFail();

        $this->get("/core/v1/collections/personas/{$persona->id}/image.png");

        Event::assertDispatched(EndpointHit::class, function (EndpointHit $event) use ($persona) {
            return ($event->getAction() === Audit::ACTION_READ) &&
                ($event->getModel()->id === $persona->id);
        });
    }

    /*
     * Upload a specific persona collection's image.
     */

    public function test_super_admin_can_upload_image()
    {
        /**
         * @var \App\Models\User $user
         */
        $user = factory(User::class)->create();
        $user->makeSuperAdmin();
        $randomCategory = Taxonomy::category()->children()->inRandomOrder()->firstOrFail();
        $image = Storage::disk('local')->get('/test-data/image.png');

        Passport::actingAs($user);

        $imageResponse = $this->json('POST', '/core/v1/files', [
            'is_private' => false,
            'mime_type' => 'image/png',
            'file' => 'data:image/png;base64,' . base64_encode($image),
        ]);

        $response = $this->json('POST', '/core/v1/collections/personas', [
            'name' => 'Test Persona',
            'intro' => 'Lorem ipsum',
            'subtitle' => 'Subtitle here',
            'order' => 1,
            'homepage' => false,
            'sideboxes' => [],
            'category_taxonomies' => [$randomCategory->id],
            'image_file_id' => $this->getResponseContent($imageResponse, 'data.id'),
        ]);

        $response->assertStatus(Response::HTTP_CREATED);
        $collectionArray = $this->getResponseContent($response)['data'];
        $content = $this->get("/core/v1/collections/personas/{$collectionArray['id']}/image.png")->content();
        $this->assertEquals($image, $content);
    }

    /*
     * Delete a specific persona collection's image.
     */

    public function test_super_admin_can_delete_image()
    {
        /**
         * @var \App\Models\User $user
         */
        $user = factory(User::class)->create();
        $user->makeSuperAdmin();
        $persona = Collection::personas()->inRandomOrder()->firstOrFail();
        $meta = $persona->meta;
        $meta['image_file_id'] = factory(File::class)->create()->id;
        $persona->meta = $meta;
        $persona->save();

        Passport::actingAs($user);

        $response = $this->json('PUT', "/core/v1/collections/personas/{$persona->id}", [
            'slug' => $persona->slug,
            'name' => $persona->name,
            'intro' => $persona->meta['intro'],
            'subtitle' => $persona->meta['subtitle'],
            'order' => $persona->order,
            'homepage' => $persona->homepage,
            'sideboxes' => [],
            'category_taxonomies' => $persona->taxonomies()->pluck(table(Taxonomy::class, 'id')),
            'image_file_id' => null,
        ]);

        $response->assertStatus(Response::HTTP_OK);
        $persona = $persona->fresh();
        $this->assertEquals(null, $persona->meta['image_file_id']);
    }
}
