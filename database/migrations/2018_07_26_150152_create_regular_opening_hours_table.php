<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRegularOpeningHoursTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('regular_opening_hours', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->customForeignUuid('service_location_id', 'service_locations');
            $table->enum('frequency', ['weekly', 'monthly', 'fortnightly', 'nth_occurrence_of_month']);
            $table->unsignedTinyInteger('weekday')->nullable();
            $table->unsignedTinyInteger('day_of_month')->nullable();
            $table->unsignedTinyInteger('occurrence_of_month')->nullable();
            $table->date('starts_at')->nullable();
            $table->time('opens_at');
            $table->time('closes_at');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::dropIfExists('regular_opening_hours');
    }
}
