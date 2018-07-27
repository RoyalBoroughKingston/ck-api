<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

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
        DB::table('taxonomies')->insert([
            'id' => uuid(),
            'name' => 'category',
            'order' => 0,
            'created_at' => $this->now,
            'updated_at' => $this->now,
        ]);

        // TODO: Add Open Eligibility records.
    }

    /**
     * Seed the organisation taxonomies.
     */
    protected function seedOrganisationTaxonomies()
    {
        DB::table('taxonomies')->insert([
            'id' => uuid(),
            'name' => 'organisation',
            'order' => 0,
            'created_at' => $this->now,
            'updated_at' => $this->now,
        ]);
    }
}
