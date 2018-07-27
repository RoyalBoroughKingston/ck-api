<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class TaxonomiesTableSeeder extends Seeder
{
    /**
     * @var \Illuminate\Support\Carbon
     */
    protected $now;

    /**
     * TaxonomiesTableSeeder constructor.
     */
    public function __construct()
    {
        $this->now = now();
    }

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $this->seedCategoryTaxonomies();
        $this->seedOrganisationTaxonomies();
    }

    /**
     * Seed the category taxonomies.
     */
    protected function seedCategoryTaxonomies()
    {
        $categoryId = uuid();

        DB::table('taxonomies')->insert([
            'id' => $categoryId,
            'name' => 'Category',
            'order' => 0,
            'created_at' => $this->now,
            'updated_at' => $this->now,
        ]);

        // TODO: Add Open Eligibility records.
        $taxonomies = $this->loadOpenEligibilityTaxonomies();
        $taxonomies = $this->normaliseOpenEligibilityTaxonomies($categoryId, $taxonomies);

        DB::table('taxonomies')->insert($taxonomies);
    }

    /**
     * Load the Open Eligibility taxonomies into an array.
     *
     * @return array
     * @throws \Illuminate\Contracts\Filesystem\FileNotFoundException
     */
    protected function loadOpenEligibilityTaxonomies(): array
    {
        $fileContents = Storage::disk('local')->get('/open-eligibility/taxonomy.json');

        $taxonomies = json_decode($fileContents, true);

        return $taxonomies;
    }

    /**
     * Normalise the taxonomies so they can be easily inserted into the database.
     *
     * @param string $categoryId
     * @param array $taxonomies
     * @return array
     */
    protected function normaliseOpenEligibilityTaxonomies(string $categoryId, array $taxonomies): array
    {
        /**
         * A mapping from the original key to the UUID.
         *
         * original_key => uuid
         */
        $idMap = [];

        /**
         * The order mapping for a taxonomy. Stores how many children it contains.
         * Includes a default entry for the parent/top-level taxonomies with no parent.
         *
         * parent_id => count
         */
        $orderMap = ['0' => 0];

        // Assign a UUID to each taxonomy.
        foreach ($taxonomies as $taxonomy) {
            $id = $taxonomy['id'];

            if (!in_array($id, $idMap)) {
                $idMap[$id] = uuid();
                $orderMap[$id] = 0;
            }
        }

        // Normalise the taxonomies.
        $normalised = array_map(function (array $taxonomy) use ($categoryId, $idMap, &$orderMap) {
            $id = $taxonomy['id'];
            $parentId = $taxonomy['parent_id'] === '0' ? '0' : $taxonomy['parent_id'];
            $name = $taxonomy['name'];
            $order = ++$orderMap[$parentId];

            return [
                'id' => $idMap[$id],
                'parent_id' => $parentId === '0' ? $categoryId : $idMap[$parentId],
                'name' => $name,
                'order' => $order,
                'created_at' => $this->now,
                'updated_at' => $this->now,
            ];
        }, $taxonomies);

        return $normalised;
    }

    /**
     * Seed the organisation taxonomies.
     */
    protected function seedOrganisationTaxonomies()
    {
        DB::table('taxonomies')->insert([
            'id' => uuid(),
            'name' => 'Organisation',
            'order' => 0,
            'created_at' => $this->now,
            'updated_at' => $this->now,
        ]);
    }
}
