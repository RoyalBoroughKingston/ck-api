<?php

namespace Tests\Feature;

use App\Models\Organisation;
use App\Models\Service;
use App\Models\Taxonomy;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Response;
use Illuminate\Support\Carbon;
use Laravel\Passport\Passport;
use Tests\TestCase;

class TaxonomyCategoriesTest extends TestCase
{
    /*
     * List all the category taxonomies.
     */

    public function test_guest_can_list_them()
    {
        $response = $this->json('GET', '/core/v1/taxonomies/categories');

        $randomTaxonomy = null;
        Taxonomy::chunk(200, function (Collection $taxonomies) use (&$randomTaxonomy) {
            foreach ($taxonomies as $taxonomy) {
                if ($taxonomy->children()->count() === 0) {
                    $randomTaxonomy = $taxonomy;
                    return false;
                }
            }
        });

        $response->assertStatus(Response::HTTP_OK);
        $response->assertJsonFragment([
            [
                'id' => $randomTaxonomy->id,
                'parent_id' => $randomTaxonomy->parent_id,
                'name' => $randomTaxonomy->name,
                'order' => $randomTaxonomy->order,
                'children' => [],
                'created_at' => $randomTaxonomy->created_at->format(Carbon::ISO8601),
                'updated_at' => $randomTaxonomy->updated_at->format(Carbon::ISO8601),
            ]
        ]);
    }

    /*
     * Create a category taxonomy.
     */

    public function test_guest_cannot_create_one()
    {
        $response = $this->json('POST', '/core/v1/taxonomies/categories');

        $response->assertStatus(Response::HTTP_UNAUTHORIZED);
    }

    public function test_service_worker_cannot_create_one()
    {
        $service = factory(Service::class)->create();
        $user = factory(User::class)->create()->makeServiceWorker($service);

        Passport::actingAs($user);

        $response = $this->json('POST', '/core/v1/taxonomies/categories');

        $response->assertStatus(Response::HTTP_FORBIDDEN);
    }

    public function test_service_admin_cannot_create_one()
    {
        $service = factory(Service::class)->create();
        $user = factory(User::class)->create()->makeServiceAdmin($service);

        Passport::actingAs($user);

        $response = $this->json('POST', '/core/v1/taxonomies/categories');

        $response->assertStatus(Response::HTTP_FORBIDDEN);
    }

    public function test_organisation_admin_cannot_create_one()
    {
        $organisation = factory(Organisation::class)->create();
        $user = factory(User::class)->create()->makeOrganisationAdmin($organisation);

        Passport::actingAs($user);

        $response = $this->json('POST', '/core/v1/taxonomies/categories');

        $response->assertStatus(Response::HTTP_FORBIDDEN);
    }

    public function test_global_admin_cannot_create_one()
    {
        $user = factory(User::class)->create()->makeGlobalAdmin();

        Passport::actingAs($user);

        $response = $this->json('POST', '/core/v1/taxonomies/categories');

        $response->assertStatus(Response::HTTP_FORBIDDEN);
    }

    public function test_super_admin_can_create_one()
    {
        $user = factory(User::class)->create()->makeSuperAdmin();
        $siblingCount = Taxonomy::category()->children()->count();
        $payload = [
            'parent_id' => null,
            'name' => 'PHPUnit Taxonomy Category Test',
            'order' => $siblingCount + 1,
        ];

        Passport::actingAs($user);
        $response = $this->json('POST', '/core/v1/taxonomies/categories', $payload);

        $response->assertStatus(Response::HTTP_CREATED);
        $response->assertJsonFragment($payload);
    }

    public function test_order_is_updated_when_created_at_beginning()
    {
        $user = factory(User::class)->create()->makeSuperAdmin();
        $topLevelCategories = Taxonomy::category()->children()->orderBy('order')->get();
        $payload = [
            'parent_id' => null,
            'name' => 'PHPUnit Taxonomy Category Test',
            'order' => 1,
        ];

        Passport::actingAs($user);
        $response = $this->json('POST', '/core/v1/taxonomies/categories', $payload);

        $response->assertStatus(Response::HTTP_CREATED);
        $response->assertJsonFragment($payload);
        foreach ($topLevelCategories as $category) {
            $this->assertDatabaseHas((new Taxonomy())->getTable(), ['id' => $category->id, 'order' => $category->order + 1]);
        }
    }

    public function test_order_is_updated_when_created_at_middle()
    {
        $user = factory(User::class)->create()->makeSuperAdmin();
        $topLevelCategories = Taxonomy::category()->children()->orderBy('order')->get();
        $payload = [
            'parent_id' => null,
            'name' => 'PHPUnit Taxonomy Category Test',
            'order' => 2,
        ];

        Passport::actingAs($user);
        $response = $this->json('POST', '/core/v1/taxonomies/categories', $payload);

        $response->assertStatus(Response::HTTP_CREATED);
        $response->assertJsonFragment($payload);
        foreach ($topLevelCategories as $category) {
            if ($category->order < 2) {
                $this->assertDatabaseHas((new Taxonomy())->getTable(), ['id' => $category->id, 'order' => $category->order]);
            } else {
                $this->assertDatabaseHas((new Taxonomy())->getTable(), ['id' => $category->id, 'order' => $category->order + 1]);
            }
        }
    }

    public function test_order_is_updated_when_created_at_end()
    {
        $user = factory(User::class)->create()->makeSuperAdmin();
        $topLevelCategories = Taxonomy::category()->children()->orderBy('order')->get();
        $payload = [
            'parent_id' => null,
            'name' => 'PHPUnit Taxonomy Category Test',
            'order' => $topLevelCategories->count() + 1,
        ];

        Passport::actingAs($user);
        $response = $this->json('POST', '/core/v1/taxonomies/categories', $payload);

        $response->assertStatus(Response::HTTP_CREATED);
        $response->assertJsonFragment($payload);
        foreach ($topLevelCategories as $category) {
            $this->assertDatabaseHas((new Taxonomy())->getTable(), ['id' => $category->id, 'order' => $category->order]);
        }
    }

    public function test_order_cannot_be_less_than_1_when_created()
    {
        $user = factory(User::class)->create()->makeSuperAdmin();
        $payload = [
            'parent_id' => null,
            'name' => 'PHPUnit Taxonomy Category Test',
            'order' => 0,
        ];

        Passport::actingAs($user);
        $response = $this->json('POST', '/core/v1/taxonomies/categories', $payload);

        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
    }

    public function test_order_cannot_be_greater_than_count_plus_1_when_created()
    {
        $user = factory(User::class)->create()->makeSuperAdmin();
        $siblingCount = Taxonomy::category()->children()->count();
        $payload = [
            'parent_id' => null,
            'name' => 'PHPUnit Taxonomy Category Test',
            'order' => $siblingCount + 2,
        ];

        Passport::actingAs($user);
        $response = $this->json('POST', '/core/v1/taxonomies/categories', $payload);

        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
    }

    /*
     * Get a specific category taxonomy
     */

    public function test_guest_can_view_one()
    {
        $randomTaxonomy = null;
        Taxonomy::chunk(200, function (Collection $taxonomies) use (&$randomTaxonomy) {
            foreach ($taxonomies as $taxonomy) {
                if ($taxonomy->children()->count() === 0) {
                    $randomTaxonomy = $taxonomy;
                    return false;
                }
            }
        });

        $response = $this->json('GET', "/core/v1/taxonomies/categories/{$randomTaxonomy->id}");

        $response->assertStatus(Response::HTTP_OK);
        $response->assertJsonFragment([
            [
                'id' => $randomTaxonomy->id,
                'parent_id' => $randomTaxonomy->parent_id,
                'name' => $randomTaxonomy->name,
                'order' => $randomTaxonomy->order,
                'children' => [],
                'created_at' => $randomTaxonomy->created_at->format(Carbon::ISO8601),
                'updated_at' => $randomTaxonomy->updated_at->format(Carbon::ISO8601),
            ]
        ]);
    }

    /*
     * Update a specific category taxonomy.
     */

    /*
     * Delete a specific category taxonomy.
     */
}
