<?php

namespace Tests\Feature;

use App\Models\Organisation;
use App\Models\Service;
use App\Models\Taxonomy;
use App\Models\User;
use Illuminate\Http\Response;
use Illuminate\Support\Carbon;
use Laravel\Passport\Passport;
use Tests\TestCase;

class TaxonomyOrganisationsTest extends TestCase
{
    /*
     * List all the organisation taxonomies.
     */

    public function test_guest_can_list_them()
    {
        $taxonomy = $this->createTaxonomyOrganisation();

        $response = $this->json('GET', '/core/v1/taxonomies/organisations');

        $response->assertStatus(Response::HTTP_OK);
        $response->assertJsonFragment([
            [
                'id' => $taxonomy->id,
                'name' => $taxonomy->name,
                'order' => $taxonomy->order,
                'created_at' => $taxonomy->created_at->format(Carbon::ISO8601),
                'updated_at' => $taxonomy->updated_at->format(Carbon::ISO8601),
            ]
        ]);
    }

    /*
     * Create an organisation taxonomy.
     */

    public function test_guest_cannot_create_one()
    {
        $response = $this->json('POST', '/core/v1/taxonomies/organisations');

        $response->assertStatus(Response::HTTP_UNAUTHORIZED);
    }

    public function test_service_worker_cannot_create_one()
    {
        $service = factory(Service::class)->create();
        $user = factory(User::class)->create()->makeServiceWorker($service);

        Passport::actingAs($user);

        $response = $this->json('POST', '/core/v1/taxonomies/organisations');

        $response->assertStatus(Response::HTTP_FORBIDDEN);
    }

    public function test_service_admin_cannot_create_one()
    {
        $service = factory(Service::class)->create();
        $user = factory(User::class)->create()->makeServiceAdmin($service);

        Passport::actingAs($user);

        $response = $this->json('POST', '/core/v1/taxonomies/organisations');

        $response->assertStatus(Response::HTTP_FORBIDDEN);
    }

    public function test_organisation_admin_cannot_create_one()
    {
        $organisation = factory(Organisation::class)->create();
        $user = factory(User::class)->create()->makeOrganisationAdmin($organisation);

        Passport::actingAs($user);

        $response = $this->json('POST', '/core/v1/taxonomies/organisations');

        $response->assertStatus(Response::HTTP_FORBIDDEN);
    }

    public function test_global_admin_cannot_create_one()
    {
        $user = factory(User::class)->create()->makeGlobalAdmin();

        Passport::actingAs($user);

        $response = $this->json('POST', '/core/v1/taxonomies/organisations');

        $response->assertStatus(Response::HTTP_FORBIDDEN);
    }

    public function test_super_admin_can_create_one()
    {
        $user = factory(User::class)->create()->makeSuperAdmin();
        $siblingCount = Taxonomy::organisation()->children()->count();
        $payload = [
            'name' => 'PHPUnit Taxonomy Organisation Test',
            'order' => $siblingCount + 1,
        ];

        Passport::actingAs($user);
        $response = $this->json('POST', '/core/v1/taxonomies/organisations', $payload);

        $response->assertStatus(Response::HTTP_CREATED);
        $response->assertJsonFragment($payload);
    }

    public function test_order_is_updated_when_created_at_beginning()
    {
        $this->createTaxonomyOrganisation();
        $this->createTaxonomyOrganisation();
        $this->createTaxonomyOrganisation();

        $user = factory(User::class)->create()->makeSuperAdmin();
        $taxonomyOrganisation = Taxonomy::organisation()->children()->orderBy('order')->get();
        $payload = [
            'name' => 'PHPUnit Taxonomy Organisation Test',
            'order' => 1,
        ];

        Passport::actingAs($user);
        $response = $this->json('POST', '/core/v1/taxonomies/organisations', $payload);

        $response->assertStatus(Response::HTTP_CREATED);
        $response->assertJsonFragment($payload);
        foreach ($taxonomyOrganisation as $organisation) {
            $this->assertDatabaseHas((new Taxonomy())->getTable(), ['id' => $organisation->id, 'order' => $organisation->order + 1]);
        }
    }

    public function test_order_is_updated_when_created_at_middle()
    {
        $this->createTaxonomyOrganisation();
        $this->createTaxonomyOrganisation();
        $this->createTaxonomyOrganisation();

        $user = factory(User::class)->create()->makeSuperAdmin();
        $taxonomyOrganisations = Taxonomy::organisation()->children()->orderBy('order')->get();
        $payload = [
            'name' => 'PHPUnit Taxonomy Organisation Test',
            'order' => 2,
        ];

        Passport::actingAs($user);
        $response = $this->json('POST', '/core/v1/taxonomies/organisations', $payload);

        $response->assertStatus(Response::HTTP_CREATED);
        $response->assertJsonFragment($payload);
        foreach ($taxonomyOrganisations as $organisation) {
            if ($organisation->order < 2) {
                $this->assertDatabaseHas((new Taxonomy())->getTable(), ['id' => $organisation->id, 'order' => $organisation->order]);
            } else {
                $this->assertDatabaseHas((new Taxonomy())->getTable(), ['id' => $organisation->id, 'order' => $organisation->order + 1]);
            }
        }
    }

    public function test_order_is_updated_when_created_at_end()
    {
        $this->createTaxonomyOrganisation();
        $this->createTaxonomyOrganisation();
        $this->createTaxonomyOrganisation();

        $user = factory(User::class)->create()->makeSuperAdmin();
        $taxonomyOrganisations = Taxonomy::organisation()->children()->orderBy('order')->get();
        $payload = [
            'name' => 'PHPUnit Taxonomy Organisation Test',
            'order' => $taxonomyOrganisations->count() + 1,
        ];

        Passport::actingAs($user);
        $response = $this->json('POST', '/core/v1/taxonomies/organisations', $payload);

        $response->assertStatus(Response::HTTP_CREATED);
        $response->assertJsonFragment($payload);
        foreach ($taxonomyOrganisations as $organisation) {
            $this->assertDatabaseHas((new Taxonomy())->getTable(), ['id' => $organisation->id, 'order' => $organisation->order]);
        }
    }

    public function test_order_cannot_be_less_than_1_when_created()
    {
        $user = factory(User::class)->create()->makeSuperAdmin();
        $payload = [
            'name' => 'PHPUnit Taxonomy Organisation Test',
            'order' => 0,
        ];

        Passport::actingAs($user);
        $response = $this->json('POST', '/core/v1/taxonomies/organisations', $payload);

        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
    }

    public function test_order_cannot_be_greater_than_count_plus_1_when_created()
    {
        $user = factory(User::class)->create()->makeSuperAdmin();
        $siblingCount = Taxonomy::organisation()->children()->count();
        $payload = [
            'name' => 'PHPUnit Taxonomy Organisation Test',
            'order' => $siblingCount + 2,
        ];

        Passport::actingAs($user);
        $response = $this->json('POST', '/core/v1/taxonomies/organisations', $payload);

        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
    }

    /*
     * Get a specific organisation taxonomy.
     */

    public function test_guest_can_view_one()
    {
        $taxonomy = $this->createTaxonomyOrganisation();

        $response = $this->json('GET', "/core/v1/taxonomies/organisations/{$taxonomy->id}");

        $response->assertStatus(Response::HTTP_OK);
        $response->assertJson([
            'data' => [
                'id' => $taxonomy->id,
                'name' => $taxonomy->name,
                'order' => $taxonomy->order,
                'created_at' => $taxonomy->created_at->format(Carbon::ISO8601),
                'updated_at' => $taxonomy->updated_at->format(Carbon::ISO8601),
            ]
        ]);
    }

    /*
     * Update a specific organisation taxonomy.
     */

    /*
     * Delete a specific organisation taxonomy.
     */

    /*
     * Helpers.
     */

    protected function createTaxonomyOrganisation(): Taxonomy
    {
        $count = Taxonomy::organisation()->children()->count();

        return Taxonomy::organisation()->children()->create([
            'name' => 'PHPUnit Organisation',
            'order' => $count + 1,
        ]);
    }
}
