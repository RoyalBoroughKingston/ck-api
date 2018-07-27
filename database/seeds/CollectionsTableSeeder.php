<?php

use Illuminate\Database\Seeder;

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
        // TODO: Confirm categories and seed records for them.

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
