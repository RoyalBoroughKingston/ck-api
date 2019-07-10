<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddUniqueConstraintsToServiceLocationsTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::table('service_locations', function (Blueprint $table) {
            $table->unique(['service_id', 'location_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::table('service_locations', function (Blueprint $table) {
            $table->dropUnique(['service_id', 'location_id']);
        });
    }
}
