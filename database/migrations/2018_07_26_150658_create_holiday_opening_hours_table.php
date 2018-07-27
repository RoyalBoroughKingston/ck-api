<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateHolidayOpeningHoursTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('holiday_opening_hours', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('service_location_id', 'service_locations');
            $table->boolean('is_closed');
            $table->date('starts_at');
            $table->date('ends_at');
            $table->time('opens_at');
            $table->time('closes_at');
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
        Schema::dropIfExists('holiday_opening_hours');
    }
}
