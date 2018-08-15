<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCachedGeocodeResultsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('cached_geocode_results', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('query')->unique();
            $table->decimal('lat', 9, 6)->nullable();
            $table->decimal('lon', 9, 6)->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('cached_geocode_results');
    }
}
