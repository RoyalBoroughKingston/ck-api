<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddUniqueConstraintsToServiceTaxonomiesTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::table('service_taxonomies', function (Blueprint $table) {
            $table->unique(['service_id', 'taxonomy_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::table('service_taxonomies', function (Blueprint $table) {
            $table->dropUnique(['service_id', 'taxonomy_id']);
        });
    }
}
