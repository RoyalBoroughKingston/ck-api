<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateRegularOpeningHoursTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('regular_opening_hours', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('service_location_id', 'service_locations');
            $table->enum('frequency', ['weekly', 'monthly', 'fortnightly', 'nth_week_of_month']);
            $table->unsignedTinyInteger('weekday')->nullable();
            $table->unsignedTinyInteger('week_of_month')->nullable();
            $table->date('starts_at')->nullable();
            $table->dateTime('opens_at');
            $table->dateTime('closes_at');
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
        Schema::dropIfExists('regular_opening_hours');
    }
}
