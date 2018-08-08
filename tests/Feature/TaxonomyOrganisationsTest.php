<?php

namespace Tests\Feature;

use App\Models\Taxonomy;
use Illuminate\Http\Response;
use Illuminate\Support\Carbon;
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

    /*
     * Get a specific organisation taxonomy.
     */

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
