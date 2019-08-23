<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddIndexesToTaxonomiesTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::table('taxonomies', function (Blueprint $table) {
            $table->index('name');
            $table->index('order');
            $table->index('created_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::table('taxonomies', function (Blueprint $table) {
            $table->dropIndex(['name']);
            $table->dropIndex(['order']);
            $table->dropIndex(['created_at']);
        });
    }
}
