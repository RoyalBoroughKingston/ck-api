<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Storage;

class SeedDefaultTaxonomyData extends Migration
{
    /**
     * @var \Illuminate\Support\Carbon
     */
    protected $now;

    /**
     * Run the migrations.
     *
     * @return void
     * @throws \Illuminate\Contracts\Filesystem\FileNotFoundException
     */
    public function up()
    {
        $this->now = now();
        $taxonomies = $this->loadOpenEligibilityTaxonomies();
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        DB::table('taxonomies')->insert($taxonomies);
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::table('taxonomies')->truncate();
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
}
