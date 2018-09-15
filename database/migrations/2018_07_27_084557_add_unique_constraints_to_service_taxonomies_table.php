<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddUniqueConstraintsToServiceTaxonomiesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('service_taxonomies', function (Blueprint $table) {
            $table->unique(['service_id', 'taxonomy_id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('service_taxonomies', function (Blueprint $table) {
            $table->dropUnique(['service_id', 'taxonomy_id']);
        });
    }
}
