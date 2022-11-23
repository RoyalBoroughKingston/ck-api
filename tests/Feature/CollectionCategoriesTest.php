<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\File;
use App\Models\User;
use App\Models\Audit;
use App\Models\Service;
use App\Models\Taxonomy;
use App\Models\Collection;
use App\Events\EndpointHit;
use Carbon\CarbonImmutable;
use Illuminate\Support\Str;
use App\Models\Organisation;
use Illuminate\Http\Response;
use Laravel\Passport\Passport;
use App\Models\CollectionTaxonomy;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Storage;

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
            'order',
            'image_file_id',
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

        $this->json('GET', '/core/v1/collections/categories');

        Event::assertDispatched(EndpointHit::class, function (EndpointHit $event) {
            return ($event->getAction() === Audit::ACTION_READ);
        });
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

        $image = factory(File::class)->states('pending-assignment')->create([
            'filename' => Str::random() . '.svg',
            'mime_type' => 'image/svg+xml',
        ]);

        $base64Image = 'data:image/svg+xml;base64,' . base64_encode(Storage::disk('local')->get('/test-data/image.svg'));

        $image->uploadBase64EncodedFile($base64Image);

        Passport::actingAs($user);

        $response = $this->json('POST', '/core/v1/collections/categories', [
            'name' => 'Test Category',
            'intro' => 'Lorem ipsum',
            'image_file_id' => $image->id,
            'order' => 1,
            'sideboxes' => [
                [
                    'title' => 'Sidebox title',
                    'content' => 'Sidebox content',
                ],
            ],
            'category_taxonomies' => [$randomCategory->id],
        ]);

        $response->assertStatus(Response::HTTP_CREATED);
        $response->assertJsonResource([
            'id',
            'name',
            'intro',
            'order',
            'image_file_id',
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
            'name' => 'Test Category',
            'intro' => 'Lorem ipsum',
            'order' => 1,
            'sideboxes' => [
                [
                    'title' => 'Sidebox title',
                    'content' => 'Sidebox content',
                ],
            ],
        ]);
        $response->assertJsonFragment([
            'id' => $randomCategory->id,
        ]);
    }

    public function test_super_admin_cannot_create_one_without_an_image()
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
            'order' => 1,
            'sideboxes' => [
                [
                    'title' => 'Sidebox title',
                    'content' => 'Sidebox content',
                ],
            ],
            'category_taxonomies' => [$randomCategory->id],
        ]);

        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);

        $response = $this->json('POST', '/core/v1/collections/categories', [
            'name' => 'Test Category',
            'intro' => 'Lorem ipsum',
            'image_file_id' => '',
            'order' => 1,
            'sideboxes' => [
                [
                    'title' => 'Sidebox title',
                    'content' => 'Sidebox content',
                ],
            ],
            'category_taxonomies' => [$randomCategory->id],
        ]);

        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);

        $response = $this->json('POST', '/core/v1/collections/categories', [
            'name' => 'Test Category',
            'intro' => 'Lorem ipsum',
            'image_file_id' => uuid(),
            'order' => 1,
            'sideboxes' => [
                [
                    'title' => 'Sidebox title',
                    'content' => 'Sidebox content',
                ],
            ],
            'category_taxonomies' => [$randomCategory->id],
        ]);

        $response->assertStatus(Response::HTTP_NOT_FOUND);
    }

    public function test_super_admin_cannot_create_one_with_an_assigned_image()
    {
        /**
         * @var \App\Models\User $user
         */
        $user = factory(User::class)->create();
        $user->makeSuperAdmin();
        $randomCategory = Taxonomy::category()->children()->inRandomOrder()->firstOrFail();

        $image = factory(File::class)->create([
            'filename' => Str::random() . '.svg',
            'mime_type' => 'image/svg+xml',
        ]);

        $base64Image = 'data:image/svg+xml;base64,' . base64_encode(Storage::disk('local')->get('/test-data/image.svg'));

        $image->uploadBase64EncodedFile($base64Image);

        Passport::actingAs($user);

        $response = $this->json('POST', '/core/v1/collections/categories', [
            'name' => 'Test Category',
            'intro' => 'Lorem ipsum',
            'image_file_id' => $image->id,
            'order' => 1,
            'sideboxes' => [
                [
                    'title' => 'Sidebox title',
                    'content' => 'Sidebox content',
                ],
            ],
            'category_taxonomies' => [$randomCategory->id],
        ]);

        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
    }

    public function test_order_is_updated_when_created_at_beginning()
    {
        // Delete the existing seeded categories.
        $this->truncateCollectionCategories();

        $image = factory(File::class)->states('pending-assignment')->create([
            'filename' => Str::random() . '.svg',
            'mime_type' => 'image/svg+xml',
        ]);

        $base64Image = 'data:image/svg+xml;base64,' . base64_encode(Storage::disk('local')->get('/test-data/image.svg'));

        $image->uploadBase64EncodedFile($base64Image);

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
                'sideboxes' => [],
            ],
        ]);
        $second = Collection::create([
            'type' => Collection::TYPE_CATEGORY,
            'name' => 'Second',
            'order' => 2,
            'meta' => [
                'intro' => 'Lorem ipsum',
                'sideboxes' => [],
            ],
        ]);
        $third = Collection::create([
            'type' => Collection::TYPE_CATEGORY,
            'name' => 'Third',
            'order' => 3,
            'meta' => [
                'intro' => 'Lorem ipsum',
                'sideboxes' => [],
            ],
        ]);

        Passport::actingAs($user);

        $response = $this->json('POST', '/core/v1/collections/categories', [
            'name' => 'Test Category',
            'intro' => 'Lorem ipsum',
            'image_file_id' => $image->id,
            'order' => 1,
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
        // Delete the existing seeded categories.
        $this->truncateCollectionCategories();

        $image = factory(File::class)->states('pending-assignment')->create([
            'filename' => Str::random() . '.svg',
            'mime_type' => 'image/svg+xml',
        ]);

        $base64Image = 'data:image/svg+xml;base64,' . base64_encode(Storage::disk('local')->get('/test-data/image.svg'));

        $image->uploadBase64EncodedFile($base64Image);

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
                'sideboxes' => [],
            ],
        ]);
        $second = Collection::create([
            'type' => Collection::TYPE_CATEGORY,
            'name' => 'Second',
            'order' => 2,
            'meta' => [
                'intro' => 'Lorem ipsum',
                'sideboxes' => [],
            ],
        ]);
        $third = Collection::create([
            'type' => Collection::TYPE_CATEGORY,
            'name' => 'Third',
            'order' => 3,
            'meta' => [
                'intro' => 'Lorem ipsum',
                'sideboxes' => [],
            ],
        ]);

        Passport::actingAs($user);

        $response = $this->json('POST', '/core/v1/collections/categories', [
            'name' => 'Test Category',
            'intro' => 'Lorem ipsum',
            'image_file_id' => $image->id,
            'order' => 2,
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
        // Delete the existing seeded categories.
        $this->truncateCollectionCategories();

        $image = factory(File::class)->states('pending-assignment')->create([
            'filename' => Str::random() . '.svg',
            'mime_type' => 'image/svg+xml',
        ]);

        $base64Image = 'data:image/svg+xml;base64,' . base64_encode(Storage::disk('local')->get('/test-data/image.svg'));

        $image->uploadBase64EncodedFile($base64Image);

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
                'sideboxes' => [],
            ],
        ]);
        $second = Collection::create([
            'type' => Collection::TYPE_CATEGORY,
            'name' => 'Second',
            'order' => 2,
            'meta' => [
                'intro' => 'Lorem ipsum',
                'sideboxes' => [],
            ],
        ]);
        $third = Collection::create([
            'type' => Collection::TYPE_CATEGORY,
            'name' => 'Third',
            'order' => 3,
            'meta' => [
                'intro' => 'Lorem ipsum',
                'sideboxes' => [],
            ],
        ]);

        Passport::actingAs($user);

        $response = $this->json('POST', '/core/v1/collections/categories', [
            'name' => 'Test Category',
            'intro' => 'Lorem ipsum',
            'image_file_id' => $image->id,
            'order' => 4,
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
        // Delete the existing seeded categories.
        $this->truncateCollectionCategories();

        $image = factory(File::class)->create([
            'filename' => Str::random() . '.svg',
            'mime_type' => 'image/svg+xml',
        ]);

        $base64Image = 'data:image/svg+xml;base64,' . base64_encode(Storage::disk('local')->get('/test-data/image.svg'));

        $image->uploadBase64EncodedFile($base64Image);

        /**
         * @var \App\Models\User $user
         */
        $user = factory(User::class)->create();
        $user->makeSuperAdmin();

        Passport::actingAs($user);

        $response = $this->json('POST', '/core/v1/collections/categories', [
            'name' => 'Test Category',
            'intro' => 'Lorem ipsum',
            'image_file_id' => $image->id,
            'order' => 0,
            'sideboxes' => [],
            'category_taxonomies' => [],
        ]);

        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
    }

    public function test_order_cannot_be_greater_than_count_plus_1_when_created()
    {
        // Delete the existing seeded categories.
        $this->truncateCollectionCategories();

        $image = factory(File::class)->create([
            'filename' => Str::random() . '.svg',
            'mime_type' => 'image/svg+xml',
        ]);

        $base64Image = 'data:image/svg+xml;base64,' . base64_encode(Storage::disk('local')->get('/test-data/image.svg'));

        $image->uploadBase64EncodedFile($base64Image);

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
                'sideboxes' => [],
            ],
        ]);
        Collection::create([
            'type' => Collection::TYPE_CATEGORY,
            'name' => 'Second',
            'order' => 2,
            'meta' => [
                'intro' => 'Lorem ipsum',
                'sideboxes' => [],
            ],
        ]);

        Passport::actingAs($user);

        $response = $this->json('POST', '/core/v1/collections/categories', [
            'name' => 'Test Category',
            'intro' => 'Lorem ipsum',
            'image_file_id' => $image->id,
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

        $image = factory(File::class)->states('pending-assignment')->create([
            'filename' => Str::random() . '.svg',
            'mime_type' => 'image/svg+xml',
        ]);

        $base64Image = 'data:image/svg+xml;base64,' . base64_encode(Storage::disk('local')->get('/test-data/image.svg'));

        $image->uploadBase64EncodedFile($base64Image);

        Passport::actingAs($user);

        $response = $this->json('POST', '/core/v1/collections/categories', [
            'name' => 'Test Category',
            'intro' => 'Lorem ipsum',
            'image_file_id' => $image->id,
            'order' => 1,
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
            'order',
            'image_file_id',
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
            'id' => $collectionCategory->id,
            'name' => $collectionCategory->name,
            'intro' => $collectionCategory->meta['intro'],
            'order' => $collectionCategory->order,
            'sideboxes' => $collectionCategory->meta['sideboxes'],
            'created_at' => $collectionCategory->created_at->format(CarbonImmutable::ISO8601),
            'updated_at' => $collectionCategory->updated_at->format(CarbonImmutable::ISO8601),
        ]);
    }

    public function test_audit_created_when_viewed()
    {
        $this->fakeEvents();

        $collectionCategory = Collection::categories()->inRandomOrder()->firstOrFail();

        $response = $this->json('GET', "/core/v1/collections/categories/{$collectionCategory->id}");

        Event::assertDispatched(EndpointHit::class, function (EndpointHit $event) use ($response) {
            return ($event->getAction() === Audit::ACTION_READ) &&
                ($event->getModel()->id === $this->getResponseContent($response)['data']['id']);
        });
    }

    /*
     * Get a specific categories image.
     */

    public function test_guest_can_view_image_as_svg()
    {
        $collectionCategory = Collection::categories()->inRandomOrder()->firstOrFail();

        $image = factory(File::class)->states('pending-assignment')->create([
            'filename' => Str::random() . '.svg',
            'mime_type' => 'image/svg+xml',
        ]);

        $base64Image = 'data:image/svg+xml;base64,' . base64_encode(Storage::disk('local')->get('/test-data/image.svg'));

        $image->uploadBase64EncodedFile($base64Image);

        $meta = $collectionCategory->meta;
        $meta['image_file_id'] = $image->id;
        $collectionCategory->meta = $meta;
        $collectionCategory->save();

        $response = $this->get("/core/v1/collections/categories/{$collectionCategory->id}/image.svg");

        $response->assertStatus(Response::HTTP_OK);
        $response->assertHeader('Content-Type', 'image/svg+xml');
        $this->assertEquals(Storage::disk('local')->get('/test-data/image.svg'), $response->content());
    }

    public function test_guest_can_view_image_as_png()
    {
        $collectionCategory = Collection::categories()->inRandomOrder()->firstOrFail();

        $image = factory(File::class)->states('pending-assignment')->create([
            'filename' => Str::random() . '.png',
            'mime_type' => 'image/png',
        ]);

        $base64Image = 'data:image/png;base64,' . base64_encode(Storage::disk('local')->get('/test-data/image.png'));

        $image->uploadBase64EncodedFile($base64Image);

        $meta = $collectionCategory->meta;
        $meta['image_file_id'] = $image->id;
        $collectionCategory->meta = $meta;
        $collectionCategory->save();

        $response = $this->get("/core/v1/collections/categories/{$collectionCategory->id}/image.png");
        $response->assertStatus(Response::HTTP_OK);
        $response->assertHeader('Content-Type', 'image/png');
        $this->assertEquals(Storage::disk('local')->get('/test-data/image.png'), $response->content());
    }

    public function test_guest_can_view_image_as_jpg()
    {
        $collectionCategory = Collection::categories()->inRandomOrder()->firstOrFail();

        $image = factory(File::class)->states('pending-assignment')->create([
            'filename' => Str::random() . '.png',
            'mime_type' => 'image/jpeg',
        ]);

        $base64Image = 'data:image/png;base64,' . base64_encode(Storage::disk('local')->get('/test-data/image.jpg'));

        $image->uploadBase64EncodedFile($base64Image);

        $meta = $collectionCategory->meta;
        $meta['image_file_id'] = $image->id;
        $collectionCategory->meta = $meta;
        $collectionCategory->save();

        $response = $this->get("/core/v1/collections/categories/{$collectionCategory->id}/image.jpg");
        $response->assertStatus(Response::HTTP_OK);
        $response->assertHeader('Content-Type', 'image/jpeg');
        $this->assertEquals(Storage::disk('local')->get('/test-data/image.jpg'), $response->content());
    }

    public function test_audit_created_when_image_viewed()
    {
        $this->fakeEvents();

        $collectionCategory = Collection::categories()->inRandomOrder()->firstOrFail();

        $image = factory(File::class)->states('pending-assignment')->create([
            'filename' => Str::random() . '.svg',
            'mime_type' => 'image/svg+xml',
        ]);

        $base64Image = 'data:image/svg+xml;base64,' . base64_encode(Storage::disk('local')->get('/test-data/image.svg'));

        $image->uploadBase64EncodedFile($base64Image);

        $meta = $collectionCategory->meta;
        $meta['image_file_id'] = $image->id;
        $collectionCategory->meta = $meta;
        $collectionCategory->save();

        $this->get("/core/v1/collections/categories/{$collectionCategory->id}/image.svg");

        Event::assertDispatched(EndpointHit::class, function (EndpointHit $event) use ($collectionCategory) {
            return ($event->getAction() === Audit::ACTION_READ) &&
                ($event->getModel()->id === $collectionCategory->id);
        });
    }

    public function test_default_image_returned_when_image_is_not_set()
    {
        $collectionCategory = Collection::categories()->inRandomOrder()->firstOrFail();

        $placeholder = Storage::disk('local')->get('/placeholders/collection_category.png');

        $response = $this->get("/core/v1/collections/categories/{$collectionCategory->id}/image.svg");

        $response->assertStatus(Response::HTTP_OK);
        $response->assertHeader('Content-Type', 'image/png');

        $content = $response->content();
        $this->assertEquals($placeholder, $content);
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

    public function test_global_admin_can_update_one()
    {
        /**
         * @var \App\Models\User $user
         */
        $user = factory(User::class)->create();
        $user->makeGlobalAdmin();
        $category = Collection::categories()->inRandomOrder()->firstOrFail();
        $taxonomy = Taxonomy::category()->children()->inRandomOrder()->firstOrFail();

        $image = factory(File::class)->states('pending-assignment')->create([
            'filename' => Str::random() . '.png',
            'mime_type' => 'image/jpeg',
        ]);

        $base64Image = 'data:image/png;base64,' . base64_encode(Storage::disk('local')->get('/test-data/image.jpg'));

        $image->uploadBase64EncodedFile($base64Image);

        Passport::actingAs($user);

        $response = $this->json('PUT', "/core/v1/collections/categories/{$category->id}", [
            'name' => 'Test Category',
            'intro' => 'Lorem ipsum',
            'image_file_id' => $image->id,
            'order' => 1,
            'sideboxes' => [],
            'category_taxonomies' => [$taxonomy->id],
        ]);

        $response->assertStatus(Response::HTTP_OK);
        $response->assertJsonResource([
            'id',
            'name',
            'intro',
            'order',
            'image_file_id',
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
            'name' => 'Test Category',
            'intro' => 'Lorem ipsum',
            'image_file_id' => $image->id,
            'order' => 1,
            'sideboxes' => [],
        ]);
        $response->assertJsonFragment([
            'id' => $taxonomy->id,
        ]);

        $category = $category->fresh();
        $this->assertEquals($image->id, $category->meta['image_file_id']);

        $content = $this->get("/core/v1/collections/categories/{$category->id}/image.jpg")->content();
        $this->assertEquals(Storage::disk('local')->get('/test-data/image.jpg'), $content);
    }

    public function test_global_admin_cannot_update_one_without_an_image()
    {
        /**
         * @var \App\Models\User $user
         */
        $user = factory(User::class)->create();
        $user->makeGlobalAdmin();
        $category = Collection::categories()->inRandomOrder()->firstOrFail();
        $category->save();
        $taxonomy = Taxonomy::category()->children()->inRandomOrder()->firstOrFail();

        $image = factory(File::class)->states('pending-assignment')->create([
            'filename' => Str::random() . '.svg',
            'mime_type' => 'image/svg+xml',
        ]);

        $base64Image = 'data:image/svg+xml;base64,' . base64_encode(Storage::disk('local')->get('/test-data/image.svg'));

        $image->uploadBase64EncodedFile($base64Image);

        Passport::actingAs($user);

        $response = $this->json('PUT', "/core/v1/collections/categories/{$category->id}", [
            'name' => 'Test Category',
            'intro' => 'Lorem ipsum',
            'order' => $category->order,
            'sideboxes' => [],
            'category_taxonomies' => [$taxonomy->id],
        ]);

        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);

        $response = $this->json('PUT', "/core/v1/collections/categories/{$category->id}", [
            'name' => 'Test Category',
            'intro' => 'Lorem ipsum',
            'image_file_id' => null,
            'order' => $category->order,
            'sideboxes' => [],
            'category_taxonomies' => [$taxonomy->id],
        ]);

        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
    }

    public function test_global_admin_cannot_update_one_with_an_assigned_image()
    {
        /**
         * @var \App\Models\User $user
         */
        $user = factory(User::class)->create();
        $user->makeGlobalAdmin();
        $category = Collection::categories()->inRandomOrder()->firstOrFail();
        $category->save();
        $taxonomy = Taxonomy::category()->children()->inRandomOrder()->firstOrFail();

        $image = factory(File::class)->create([
            'filename' => Str::random() . '.svg',
            'mime_type' => 'image/svg+xml',
        ]);

        $base64Image = 'data:image/svg+xml;base64,' . base64_encode(Storage::disk('local')->get('/test-data/image.svg'));

        $image->uploadBase64EncodedFile($base64Image);

        Passport::actingAs($user);

        $response = $this->json('PUT', "/core/v1/collections/categories/{$category->id}", [
            'name' => 'Test Category',
            'intro' => 'Lorem ipsum',
            'image_file_id' => $image->id,
            'order' => 1,
            'sideboxes' => [],
            'category_taxonomies' => [$taxonomy->id],
        ]);

        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
    }

    public function test_global_admin_can_update_one_without_an_image_when_changing_order()
    {
        // Delete the existing seeded categories.
        $this->truncateCollectionCategories();

        /**
         * @var \App\Models\User $user
         */
        $user = factory(User::class)->create();
        $user->makeGlobalAdmin();
        $first = Collection::create([
            'type' => Collection::TYPE_CATEGORY,
            'name' => 'First',
            'order' => 1,
            'meta' => [
                'intro' => 'Lorem ipsum',
                'sideboxes' => [],
            ],
        ]);
        $second = Collection::create([
            'type' => Collection::TYPE_CATEGORY,
            'name' => 'Second',
            'order' => 2,
            'meta' => [
                'intro' => 'Lorem ipsum',
                'sideboxes' => [],
            ],
        ]);
        $third = Collection::create([
            'type' => Collection::TYPE_CATEGORY,
            'name' => 'Third',
            'order' => 3,
            'meta' => [
                'intro' => 'Lorem ipsum',
                'sideboxes' => [],
            ],
        ]);

        Passport::actingAs($user);

        $response = $this->json('PUT', "/core/v1/collections/categories/{$third->id}", [
            'name' => 'Third',
            'intro' => 'Lorem ipsum',
            'order' => 1,
            'sideboxes' => [],
            'category_taxonomies' => [],
        ]);

        $response->assertStatus(Response::HTTP_OK);
        $this->assertDatabaseHas((new Collection())->getTable(), ['id' => $first->id, 'order' => 2]);
        $this->assertDatabaseHas((new Collection())->getTable(), ['id' => $second->id, 'order' => 3]);
        $this->assertDatabaseHas((new Collection())->getTable(), ['id' => $third->id, 'order' => 1]);
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
                'sideboxes' => [],
            ],
        ]);
        $second = Collection::create([
            'type' => Collection::TYPE_CATEGORY,
            'name' => 'Second',
            'order' => 2,
            'meta' => [
                'intro' => 'Lorem ipsum',
                'sideboxes' => [],
            ],
        ]);
        $third = Collection::create([
            'type' => Collection::TYPE_CATEGORY,
            'name' => 'Third',
            'order' => 3,
            'meta' => [
                'intro' => 'Lorem ipsum',
                'sideboxes' => [],
            ],
        ]);

        Passport::actingAs($user);

        $response = $this->json('PUT', "/core/v1/collections/categories/{$third->id}", [
            'name' => 'Third',
            'intro' => 'Lorem ipsum',
            'order' => 1,
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
                'sideboxes' => [],
            ],
        ]);
        $second = Collection::create([
            'type' => Collection::TYPE_CATEGORY,
            'name' => 'Second',
            'order' => 2,
            'meta' => [
                'intro' => 'Lorem ipsum',
                'sideboxes' => [],
            ],
        ]);
        $third = Collection::create([
            'type' => Collection::TYPE_CATEGORY,
            'name' => 'Third',
            'order' => 3,
            'meta' => [
                'intro' => 'Lorem ipsum',
                'sideboxes' => [],
            ],
        ]);

        Passport::actingAs($user);

        $response = $this->json('PUT', "/core/v1/collections/categories/{$first->id}", [
            'name' => 'First',
            'intro' => 'Lorem ipsum',
            'order' => 2,
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
                'sideboxes' => [],
            ],
        ]);
        $second = Collection::create([
            'type' => Collection::TYPE_CATEGORY,
            'name' => 'Second',
            'order' => 2,
            'meta' => [
                'intro' => 'Lorem ipsum',
                'sideboxes' => [],
            ],
        ]);
        $third = Collection::create([
            'type' => Collection::TYPE_CATEGORY,
            'name' => 'Third',
            'order' => 3,
            'meta' => [
                'intro' => 'Lorem ipsum',
                'sideboxes' => [],
            ],
        ]);

        Passport::actingAs($user);

        $response = $this->json('PUT', "/core/v1/collections/categories/{$first->id}", [
            'name' => 'First',
            'intro' => 'Lorem ipsum',
            'order' => 3,
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
                'sideboxes' => [],
            ],
        ]);

        Passport::actingAs($user);

        $response = $this->json('PUT', "/core/v1/collections/categories/{$category->id}", [
            'name' => 'First',
            'intro' => 'Lorem ipsum',
            'order' => 0,
            'sideboxes' => [],
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
                'sideboxes' => [],
            ],
        ]);

        Passport::actingAs($user);

        $response = $this->json('PUT', "/core/v1/collections/categories/{$category->id}", [
            'name' => 'First',
            'intro' => 'Lorem ipsum',
            'order' => 2,
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
        $category = Collection::categories()->inRandomOrder()->firstOrFail();
        $taxonomy = Taxonomy::category()->children()->inRandomOrder()->firstOrFail();

        Passport::actingAs($user);

        $this->json('PUT', "/core/v1/collections/categories/{$category->id}", [
            'name' => 'Test Category',
            'intro' => 'Lorem ipsum',
            'order' => 1,
            'sideboxes' => [],
            'category_taxonomies' => [$taxonomy->id],
        ]);

        Event::assertDispatched(EndpointHit::class, function (EndpointHit $event) use ($user, $category) {
            return ($event->getAction() === Audit::ACTION_UPDATE) &&
                ($event->getUser()->id === $user->id) &&
                ($event->getModel()->id === $category->id);
        });
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
                'sideboxes' => [],
            ],
        ]);
        $second = Collection::create([
            'type' => Collection::TYPE_CATEGORY,
            'name' => 'Second',
            'order' => 2,
            'meta' => [
                'intro' => 'Lorem ipsum',
                'sideboxes' => [],
            ],
        ]);
        $third = Collection::create([
            'type' => Collection::TYPE_CATEGORY,
            'name' => 'Third',
            'order' => 3,
            'meta' => [
                'intro' => 'Lorem ipsum',
                'sideboxes' => [],
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
                'sideboxes' => [],
            ],
        ]);
        $second = Collection::create([
            'type' => Collection::TYPE_CATEGORY,
            'name' => 'Second',
            'order' => 2,
            'meta' => [
                'intro' => 'Lorem ipsum',
                'sideboxes' => [],
            ],
        ]);
        $third = Collection::create([
            'type' => Collection::TYPE_CATEGORY,
            'name' => 'Third',
            'order' => 3,
            'meta' => [
                'intro' => 'Lorem ipsum',
                'sideboxes' => [],
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
                'sideboxes' => [],
            ],
        ]);
        $second = Collection::create([
            'type' => Collection::TYPE_CATEGORY,
            'name' => 'Second',
            'order' => 2,
            'meta' => [
                'intro' => 'Lorem ipsum',
                'sideboxes' => [],
            ],
        ]);
        $third = Collection::create([
            'type' => Collection::TYPE_CATEGORY,
            'name' => 'Third',
            'order' => 3,
            'meta' => [
                'intro' => 'Lorem ipsum',
                'sideboxes' => [],
            ],
        ]);

        Passport::actingAs($user);

        $response = $this->json('DELETE', "/core/v1/collections/categories/{$third->id}");

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
        $category = Collection::categories()->inRandomOrder()->firstOrFail();

        Passport::actingAs($user);

        $this->json('DELETE', "/core/v1/collections/categories/{$category->id}");

        Event::assertDispatched(EndpointHit::class, function (EndpointHit $event) use ($user, $category) {
            return ($event->getAction() === Audit::ACTION_DELETE) &&
                ($event->getUser()->id === $user->id) &&
                ($event->getModel()->id === $category->id);
        });
    }
}
