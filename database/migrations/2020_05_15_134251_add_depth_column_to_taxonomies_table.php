<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddDepthColumnToTaxonomiesTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::table('taxonomies', function (Blueprint $table) {
            $table->unsignedInteger('depth')
                ->default(0)
                ->after('order')
                ->index();
        });

        // Remove the default value.
        Schema::table('taxonomies', function (Blueprint $table) {
            $table->unsignedInteger('depth')
                ->default(null)
                ->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::table('taxonomies', function (Blueprint $table) {
            $table->dropIndex(['depth']);
            $table->dropColumn('depth');
        });
    }
}
