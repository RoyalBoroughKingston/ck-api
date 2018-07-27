<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddUniqueConstraintsToCollectionTaxonomiesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('collection_taxonomies', function (Blueprint $table) {
            $table->unique(['collection_id', 'taxonomy_id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('collection_taxonomies', function (Blueprint $table) {
            $table->dropUnique(['collection_id', 'taxonomy_id']);
        });
    }
}
