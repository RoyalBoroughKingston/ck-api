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
use Laravel\Passport\Passport;
use Tests\TestCase;

class CollectionCategoriesTest extends TestCase
{
    /*
     * List all the category collections.
     */

    public function test_guest_can_list_them()
    {
        $response = $this->json('GET', '/core/v1/collections/categories');

        $response->assertStatus(Response::HTTP_OK);
        $response->assertJsonCollection([
            'id',
            'name',
            'intro',
            'icon',
            'order',
            'category_taxonomies' => [
                '*' => [
                    'id',
                    'parent_id',
                    'name',
                    'created_at',
                    'updated_at',
                ]
            ],
            'created_at',
            'updated_at',
        ]);
    }

    /*
     * Create a collection category.
     */

    public function test_guest_cannot_create_one()
    {
        $response = $this->json('POST', '/core/v1/collections/categories');

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

        $response = $this->json('POST', '/core/v1/collections/categories');

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

        $response = $this->json('POST', '/core/v1/collections/categories');

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

        $response = $this->json('POST', '/core/v1/collections/categories');

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

        $response = $this->json('POST', '/core/v1/collections/categories');

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

        $response = $this->json('POST', '/core/v1/collections/categories', [
            'name' => 'Test Category',
            'intro' => 'Lorem ipsum',
            'icon' => 'info',
            'order' => 1,
            'category_taxonomies' => [$randomCategory->id],
        ]);

        $response->assertStatus(Response::HTTP_CREATED);
        $response->assertJsonResource([
            'id',
            'name',
            'intro',
            'icon',
            'order',
            'category_taxonomies' => [
                '*' => [
                    'id',
                    'parent_id',
                    'name',
                    'created_at',
                    'updated_at',
                ]
            ],
            'created_at',
            'updated_at',
        ]);
        $response->assertJsonFragment([
            'name' => 'Test Category',
            'intro' => 'Lorem ipsum',
            'icon' => 'info',
            'order' => 1,
        ]);
        $response->assertJsonFragment([
            'id' => $randomCategory->id,
        ]);
    }

    public function test_order_is_updated_when_created_at_beginning()
    {
        // Delete the existing seeded categories.
        $this->truncateCollectionCategories();

        /**
         * @var \App\Models\User $user
         */
        $user = factory(User::class)->create();
        $user->makeSuperAdmin();
        $first = Collection::create([
            'type' => Collection::TYPE_CATEGORY,
            'name' => 'First',
            'order' => 1,
            'meta' => [
                'intro' => 'Lorem ipsum',
                'icon' => 'info',
            ],
        ]);
        $second = Collection::create([
            'type' => Collection::TYPE_CATEGORY,
            'name' => 'Second',
            'order' => 2,
            'meta' => [
                'intro' => 'Lorem ipsum',
                'icon' => 'info',
            ],
        ]);
        $third = Collection::create([
            'type' => Collection::TYPE_CATEGORY,
            'name' => 'Third',
            'order' => 3,
            'meta' => [
                'intro' => 'Lorem ipsum',
                'icon' => 'info',
            ],
        ]);

        Passport::actingAs($user);

        $response = $this->json('POST', '/core/v1/collections/categories', [
            'name' => 'Test Category',
            'intro' => 'Lorem ipsum',
            'icon' => 'info',
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
        // Delete the existing seeded categories.
        $this->truncateCollectionCategories();

        /**
         * @var \App\Models\User $user
         */
        $user = factory(User::class)->create();
        $user->makeSuperAdmin();
        $first = Collection::create([
            'type' => Collection::TYPE_CATEGORY,
            'name' => 'First',
            'order' => 1,
            'meta' => [
                'intro' => 'Lorem ipsum',
                'icon' => 'info',
            ],
        ]);
        $second = Collection::create([
            'type' => Collection::TYPE_CATEGORY,
            'name' => 'Second',
            'order' => 2,
            'meta' => [
                'intro' => 'Lorem ipsum',
                'icon' => 'info',
            ],
        ]);
        $third = Collection::create([
            'type' => Collection::TYPE_CATEGORY,
            'name' => 'Third',
            'order' => 3,
            'meta' => [
                'intro' => 'Lorem ipsum',
                'icon' => 'info',
            ],
        ]);

        Passport::actingAs($user);

        $response = $this->json('POST', '/core/v1/collections/categories', [
            'name' => 'Test Category',
            'intro' => 'Lorem ipsum',
            'icon' => 'info',
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
        // Delete the existing seeded categories.
        $this->truncateCollectionCategories();

        /**
         * @var \App\Models\User $user
         */
        $user = factory(User::class)->create();
        $user->makeSuperAdmin();
        $first = Collection::create([
            'type' => Collection::TYPE_CATEGORY,
            'name' => 'First',
            'order' => 1,
            'meta' => [
                'intro' => 'Lorem ipsum',
                'icon' => 'info',
            ],
        ]);
        $second = Collection::create([
            'type' => Collection::TYPE_CATEGORY,
            'name' => 'Second',
            'order' => 2,
            'meta' => [
                'intro' => 'Lorem ipsum',
                'icon' => 'info',
            ],
        ]);
        $third = Collection::create([
            'type' => Collection::TYPE_CATEGORY,
            'name' => 'Third',
            'order' => 3,
            'meta' => [
                'intro' => 'Lorem ipsum',
                'icon' => 'info',
            ],
        ]);

        Passport::actingAs($user);

        $response = $this->json('POST', '/core/v1/collections/categories', [
            'name' => 'Test Category',
            'intro' => 'Lorem ipsum',
            'icon' => 'info',
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
        // Delete the existing seeded categories.
        $this->truncateCollectionCategories();

        /**
         * @var \App\Models\User $user
         */
        $user = factory(User::class)->create();
        $user->makeSuperAdmin();

        Passport::actingAs($user);

        $response = $this->json('POST', '/core/v1/collections/categories', [
            'name' => 'Test Category',
            'intro' => 'Lorem ipsum',
            'icon' => 'info',
            'order' => 0,
            'category_taxonomies' => [],
        ]);

        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
    }

    public function test_order_cannot_be_greater_than_count_plus_1_when_created()
    {
        // Delete the existing seeded categories.
        $this->truncateCollectionCategories();

        /**
         * @var \App\Models\User $user
         */
        $user = factory(User::class)->create();
        $user->makeSuperAdmin();
        Collection::create([
            'type' => Collection::TYPE_CATEGORY,
            'name' => 'First',
            'order' => 1,
            'meta' => [
                'intro' => 'Lorem ipsum',
                'icon' => 'info',
            ],
        ]);
        Collection::create([
            'type' => Collection::TYPE_CATEGORY,
            'name' => 'Second',
            'order' => 2,
            'meta' => [
                'intro' => 'Lorem ipsum',
                'icon' => 'info',
            ],
        ]);

        Passport::actingAs($user);

        $response = $this->json('POST', '/core/v1/collections/categories', [
            'name' => 'Test Category',
            'intro' => 'Lorem ipsum',
            'icon' => 'info',
            'order' => 4,
            'category_taxonomies' => [],
        ]);

        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
    }

    /*
     * Get a specific category collection.
     */

    public function test_guest_can_view_one()
    {
        $collectionCategory = Collection::categories()->inRandomOrder()->firstOrFail();

        $response = $this->json('GET', "/core/v1/collections/categories/{$collectionCategory->id}");

        $response->assertStatus(Response::HTTP_OK);
        $response->assertJsonResource([
            'id',
            'name',
            'intro',
            'icon',
            'order',
            'category_taxonomies' => [
                '*' => [
                    'id',
                    'parent_id',
                    'name',
                    'created_at',
                    'updated_at',
                ]
            ],
            'created_at',
            'updated_at',
        ]);
        $response->assertJsonFragment([
            'id' => $collectionCategory->id,
            'name' => $collectionCategory->name,
            'intro' => $collectionCategory->meta['intro'],
            'icon' => $collectionCategory->meta['icon'],
            'order' => $collectionCategory->order,
            'created_at' => $collectionCategory->created_at->format(Carbon::ISO8601),
            'updated_at' => $collectionCategory->updated_at->format(Carbon::ISO8601),
        ]);
    }

    /*
     * Update a specific category collection.
     */

    public function test_guest_cannot_update_one()
    {
        $category = Collection::categories()->inRandomOrder()->firstOrFail();

        $response = $this->json('PUT', "/core/v1/collections/categories/{$category->id}");

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
        $category = Collection::categories()->inRandomOrder()->firstOrFail();

        Passport::actingAs($user);

        $response = $this->json('PUT', "/core/v1/collections/categories/{$category->id}");

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
        $category = Collection::categories()->inRandomOrder()->firstOrFail();

        Passport::actingAs($user);

        $response = $this->json('PUT', "/core/v1/collections/categories/{$category->id}");

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
        $category = Collection::categories()->inRandomOrder()->firstOrFail();

        Passport::actingAs($user);

        $response = $this->json('PUT', "/core/v1/collections/categories/{$category->id}");

        $response->assertStatus(Response::HTTP_FORBIDDEN);
    }

    public function test_global_admin_cannot_update_one()
    {
        /**
         * @var \App\Models\User $user
         */
        $user = factory(User::class)->create();
        $user->makeGlobalAdmin();
        $category = Collection::categories()->inRandomOrder()->firstOrFail();

        Passport::actingAs($user);

        $response = $this->json('PUT', "/core/v1/collections/categories/{$category->id}");

        $response->assertStatus(Response::HTTP_FORBIDDEN);
    }

    public function test_super_admin_can_update_one()
    {
        /**
         * @var \App\Models\User $user
         */
        $user = factory(User::class)->create();
        $user->makeSuperAdmin();
        $category = Collection::categories()->inRandomOrder()->firstOrFail();
        $taxonomy = Taxonomy::category()->children()->inRandomOrder()->firstOrFail();

        Passport::actingAs($user);

        $response = $this->json('PUT', "/core/v1/collections/categories/{$category->id}", [
            'name' => 'Test Category',
            'intro' => 'Lorem ipsum',
            'icon' => 'info',
            'order' => 1,
            'category_taxonomies' => [$taxonomy->id],
        ]);

        $response->assertStatus(Response::HTTP_OK);
        $response->assertJsonResource([
            'id',
            'name',
            'intro',
            'icon',
            'order',
            'category_taxonomies' => [
                '*' => [
                    'id',
                    'parent_id',
                    'name',
                    'created_at',
                    'updated_at',
                ]
            ],
            'created_at',
            'updated_at',
        ]);
        $response->assertJsonFragment([
            'name' => 'Test Category',
            'intro' => 'Lorem ipsum',
            'icon' => 'info',
            'order' => 1,
        ]);
        $response->assertJsonFragment([
            'id' => $taxonomy->id,
        ]);
    }

    public function test_order_is_updated_when_updated_to_beginning()
    {
        // Delete the existing seeded categories.
        $this->truncateCollectionCategories();

        /**
         * @var \App\Models\User $user
         */
        $user = factory(User::class)->create();
        $user->makeSuperAdmin();
        $first = Collection::create([
            'type' => Collection::TYPE_CATEGORY,
            'name' => 'First',
            'order' => 1,
            'meta' => [
                'intro' => 'Lorem ipsum',
                'icon' => 'info',
            ],
        ]);
        $second = Collection::create([
            'type' => Collection::TYPE_CATEGORY,
            'name' => 'Second',
            'order' => 2,
            'meta' => [
                'intro' => 'Lorem ipsum',
                'icon' => 'info',
            ],
        ]);
        $third = Collection::create([
            'type' => Collection::TYPE_CATEGORY,
            'name' => 'Third',
            'order' => 3,
            'meta' => [
                'intro' => 'Lorem ipsum',
                'icon' => 'info',
            ],
        ]);

        Passport::actingAs($user);

        $response = $this->json('PUT', "/core/v1/collections/categories/{$third->id}", [
            'name' => 'Third',
            'intro' => 'Lorem ipsum',
            'icon' => 'info',
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
        // Delete the existing seeded categories.
        $this->truncateCollectionCategories();

        /**
         * @var \App\Models\User $user
         */
        $user = factory(User::class)->create();
        $user->makeSuperAdmin();
        $first = Collection::create([
            'type' => Collection::TYPE_CATEGORY,
            'name' => 'First',
            'order' => 1,
            'meta' => [
                'intro' => 'Lorem ipsum',
                'icon' => 'info',
            ],
        ]);
        $second = Collection::create([
            'type' => Collection::TYPE_CATEGORY,
            'name' => 'Second',
            'order' => 2,
            'meta' => [
                'intro' => 'Lorem ipsum',
                'icon' => 'info',
            ],
        ]);
        $third = Collection::create([
            'type' => Collection::TYPE_CATEGORY,
            'name' => 'Third',
            'order' => 3,
            'meta' => [
                'intro' => 'Lorem ipsum',
                'icon' => 'info',
            ],
        ]);

        Passport::actingAs($user);

        $response = $this->json('PUT', "/core/v1/collections/categories/{$first->id}", [
            'name' => 'First',
            'intro' => 'Lorem ipsum',
            'icon' => 'info',
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
        // Delete the existing seeded categories.
        $this->truncateCollectionCategories();

        /**
         * @var \App\Models\User $user
         */
        $user = factory(User::class)->create();
        $user->makeSuperAdmin();
        $first = Collection::create([
            'type' => Collection::TYPE_CATEGORY,
            'name' => 'First',
            'order' => 1,
            'meta' => [
                'intro' => 'Lorem ipsum',
                'icon' => 'info',
            ],
        ]);
        $second = Collection::create([
            'type' => Collection::TYPE_CATEGORY,
            'name' => 'Second',
            'order' => 2,
            'meta' => [
                'intro' => 'Lorem ipsum',
                'icon' => 'info',
            ],
        ]);
        $third = Collection::create([
            'type' => Collection::TYPE_CATEGORY,
            'name' => 'Third',
            'order' => 3,
            'meta' => [
                'intro' => 'Lorem ipsum',
                'icon' => 'info',
            ],
        ]);

        Passport::actingAs($user);

        $response = $this->json('PUT', "/core/v1/collections/categories/{$first->id}", [
            'name' => 'First',
            'intro' => 'Lorem ipsum',
            'icon' => 'info',
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
        // Delete the existing seeded categories.
        $this->truncateCollectionCategories();

        /**
         * @var \App\Models\User $user
         */
        $user = factory(User::class)->create();
        $user->makeSuperAdmin();
        $category = Collection::create([
            'type' => Collection::TYPE_CATEGORY,
            'name' => 'First',
            'order' => 1,
            'meta' => [
                'intro' => 'Lorem ipsum',
                'icon' => 'info',
            ],
        ]);

        Passport::actingAs($user);

        $response = $this->json('PUT', "/core/v1/collections/categories/{$category->id}", [
            'name' => 'First',
            'intro' => 'Lorem ipsum',
            'icon' => 'info',
            'order' => 0,
            'category_taxonomies' => [],
        ]);

        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
    }

    public function test_order_cannot_be_greater_than_count_plus_1_when_updated()
    {
        // Delete the existing seeded categories.
        $this->truncateCollectionCategories();

        /**
         * @var \App\Models\User $user
         */
        $user = factory(User::class)->create();
        $user->makeSuperAdmin();
        $category = Collection::create([
            'type' => Collection::TYPE_CATEGORY,
            'name' => 'First',
            'order' => 1,
            'meta' => [
                'intro' => 'Lorem ipsum',
                'icon' => 'info',
            ],
        ]);

        Passport::actingAs($user);

        $response = $this->json('PUT', "/core/v1/collections/categories/{$category->id}", [
            'name' => 'First',
            'intro' => 'Lorem ipsum',
            'icon' => 'info',
            'order' => 2,
            'category_taxonomies' => [],
        ]);

        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
    }

    /*
     * Delete a specific category collection.
     */

    public function test_guest_cannot_delete_one()
    {
        $category = Collection::categories()->inRandomOrder()->firstOrFail();

        $response = $this->json('DELETE', "/core/v1/collections/categories/{$category->id}");

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
        $category = Collection::categories()->inRandomOrder()->firstOrFail();

        Passport::actingAs($user);

        $response = $this->json('DELETE', "/core/v1/collections/categories/{$category->id}");

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
        $category = Collection::categories()->inRandomOrder()->firstOrFail();

        Passport::actingAs($user);

        $response = $this->json('DELETE', "/core/v1/collections/categories/{$category->id}");

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
        $category = Collection::categories()->inRandomOrder()->firstOrFail();

        Passport::actingAs($user);

        $response = $this->json('DELETE', "/core/v1/collections/categories/{$category->id}");

        $response->assertStatus(Response::HTTP_FORBIDDEN);
    }

    public function test_global_admin_cannot_delete_one()
    {
        /**
         * @var \App\Models\User $user
         */
        $user = factory(User::class)->create();
        $user->makeGlobalAdmin();
        $category = Collection::categories()->inRandomOrder()->firstOrFail();

        Passport::actingAs($user);

        $response = $this->json('DELETE', "/core/v1/collections/categories/{$category->id}");

        $response->assertStatus(Response::HTTP_FORBIDDEN);
    }

    public function test_super_admin_can_delete_one()
    {
        /**
         * @var \App\Models\User $user
         */
        $user = factory(User::class)->create();
        $user->makeSuperAdmin();
        $category = Collection::categories()->inRandomOrder()->firstOrFail();

        Passport::actingAs($user);

        $response = $this->json('DELETE', "/core/v1/collections/categories/{$category->id}");

        $response->assertStatus(Response::HTTP_OK);
        $this->assertDatabaseMissing((new Collection())->getTable(), ['id' => $category->id]);
        $this->assertDatabaseMissing((new CollectionTaxonomy())->getTable(), ['collection_id' => $category->id]);
    }

    public function test_order_is_updated_when_deleted_at_beginning()
    {
        // Delete the existing seeded categories.
        $this->truncateCollectionCategories();

        /**
         * @var \App\Models\User $user
         */
        $user = factory(User::class)->create();
        $user->makeSuperAdmin();
        $first = Collection::create([
            'type' => Collection::TYPE_CATEGORY,
            'name' => 'First',
            'order' => 1,
            'meta' => [
                'intro' => 'Lorem ipsum',
                'icon' => 'info',
            ],
        ]);
        $second = Collection::create([
            'type' => Collection::TYPE_CATEGORY,
            'name' => 'Second',
            'order' => 2,
            'meta' => [
                'intro' => 'Lorem ipsum',
                'icon' => 'info',
            ],
        ]);
        $third = Collection::create([
            'type' => Collection::TYPE_CATEGORY,
            'name' => 'Third',
            'order' => 3,
            'meta' => [
                'intro' => 'Lorem ipsum',
                'icon' => 'info',
            ],
        ]);

        Passport::actingAs($user);

        $response = $this->json('DELETE', "/core/v1/collections/categories/{$first->id}");

        $response->assertStatus(Response::HTTP_OK);
        $this->assertDatabaseMissing((new Collection())->getTable(), ['id' => $first->id]);
        $this->assertDatabaseHas((new Collection())->getTable(), ['id' => $second->id, 'order' => 1]);
        $this->assertDatabaseHas((new Collection())->getTable(), ['id' => $third->id, 'order' => 2]);
    }

    public function test_order_is_updated_when_deleted_at_middle()
    {
        // Delete the existing seeded categories.
        $this->truncateCollectionCategories();

        /**
         * @var \App\Models\User $user
         */
        $user = factory(User::class)->create();
        $user->makeSuperAdmin();
        $first = Collection::create([
            'type' => Collection::TYPE_CATEGORY,
            'name' => 'First',
            'order' => 1,
            'meta' => [
                'intro' => 'Lorem ipsum',
                'icon' => 'info',
            ],
        ]);
        $second = Collection::create([
            'type' => Collection::TYPE_CATEGORY,
            'name' => 'Second',
            'order' => 2,
            'meta' => [
                'intro' => 'Lorem ipsum',
                'icon' => 'info',
            ],
        ]);
        $third = Collection::create([
            'type' => Collection::TYPE_CATEGORY,
            'name' => 'Third',
            'order' => 3,
            'meta' => [
                'intro' => 'Lorem ipsum',
                'icon' => 'info',
            ],
        ]);

        Passport::actingAs($user);

        $response = $this->json('DELETE', "/core/v1/collections/categories/{$second->id}");

        $response->assertStatus(Response::HTTP_OK);
        $this->assertDatabaseMissing((new Collection())->getTable(), ['id' => $second->id]);
        $this->assertDatabaseHas((new Collection())->getTable(), ['id' => $first->id, 'order' => 1]);
        $this->assertDatabaseHas((new Collection())->getTable(), ['id' => $third->id, 'order' => 2]);
    }

    public function test_order_is_updated_when_deleted_at_end()
    {
        // Delete the existing seeded categories.
        $this->truncateCollectionCategories();

        /**
         * @var \App\Models\User $user
         */
        $user = factory(User::class)->create();
        $user->makeSuperAdmin();
        $first = Collection::create([
            'type' => Collection::TYPE_CATEGORY,
            'name' => 'First',
            'order' => 1,
            'meta' => [
                'intro' => 'Lorem ipsum',
                'icon' => 'info',
            ],
        ]);
        $second = Collection::create([
            'type' => Collection::TYPE_CATEGORY,
            'name' => 'Second',
            'order' => 2,
            'meta' => [
                'intro' => 'Lorem ipsum',
                'icon' => 'info',
            ],
        ]);
        $third = Collection::create([
            'type' => Collection::TYPE_CATEGORY,
            'name' => 'Third',
            'order' => 3,
            'meta' => [
                'intro' => 'Lorem ipsum',
                'icon' => 'info',
            ],
        ]);

        Passport::actingAs($user);

        $response = $this->json('DELETE', "/core/v1/collections/categories/{$third->id}");

        $response->assertStatus(Response::HTTP_OK);
        $this->assertDatabaseMissing((new Collection())->getTable(), ['id' => $third->id]);
        $this->assertDatabaseHas((new Collection())->getTable(), ['id' => $first->id, 'order' => 1]);
        $this->assertDatabaseHas((new Collection())->getTable(), ['id' => $second->id, 'order' => 2]);
    }
}