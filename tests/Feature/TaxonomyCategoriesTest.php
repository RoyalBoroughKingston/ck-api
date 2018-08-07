<?php

namespace Tests\Feature;

use App\Models\Taxonomy;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Response;
use Illuminate\Support\Carbon;
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
                'children' => [],
                'created_at' => $randomTaxonomy->created_at->format(Carbon::ISO8601),
                'updated_at' => $randomTaxonomy->updated_at->format(Carbon::ISO8601),
            ]
        ]);
    }

    /*
     * Create a category taxonomy.
     */

    /*
     * Get a specific category taxonomy
     */

    /*
     * Update a specific category taxonomy.
     */

    /*
     * Delete a specific category taxonomy.
     */
}
