<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CollectionsTableSeeder extends Seeder
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
        $this->seedCategoryCollections();
        $this->seedPersonaCollections();
    }

    /**
     * Seed the category collections.
     */
    protected function seedCategoryCollections()
    {
        $this->seedLeisureCategory();
        $this->seedSelfHelpCategory();
        $this->seedAdviceCategory();
    }

    /**
     * Seed the Leisure and Social Activities category.
     */
    protected function seedLeisureCategory()
    {
        $uuid = uuid();
        DB::table('collections')->insert([
            'id' => $uuid,
            'name' => 'Leisure and Social Activities',
            'meta' => json_encode([
                'intro' => 'Lorem ipsum',
                'icon' => 'coffee',
            ]),
            'order' => 1,
            'created_at' => $this->now,
            'updated_at' => $this->now,
        ]);

        // TODO: Seed records for collection_taxonomies pivot table.
    }

    /**
     * Seed the Self Help category.
     */
    protected function seedSelfHelpCategory()
    {
        $uuid = uuid();
        DB::table('collections')->insert([
            'id' => $uuid,
            'name' => 'Self Help',
            'meta' => json_encode([
                'intro' => 'Lorem ipsum',
                'icon' => 'life-ring',
            ]),
            'order' => 2,
            'created_at' => $this->now,
            'updated_at' => $this->now,
        ]);

        // TODO: Seed records for collection_taxonomies pivot table.
    }

    /**
     * Seed the Advice and Support Services category.
     */
    protected function seedAdviceCategory()
    {
        $uuid = uuid();
        DB::table('collections')->insert([
            'id' => $uuid,
            'name' => 'Advice and Support Services',
            'meta' => json_encode([
                'intro' => 'Lorem ipsum',
                'icon' => 'info-circle',
            ]),
            'order' => 3,
            'created_at' => $this->now,
            'updated_at' => $this->now,
        ]);

        // TODO: Seed records for collection_taxonomies pivot table.
    }

    /**
     * Seed the persona collections.
     */
    protected function seedPersonaCollections()
    {
        // TODO: Confirm personas and seed records for them.

        // TODO: Seed records for collection_taxonomies pivot table.
    }
}
