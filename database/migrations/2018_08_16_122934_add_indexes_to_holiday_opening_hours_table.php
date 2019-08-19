<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddIndexesToHolidayOpeningHoursTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::table('holiday_opening_hours', function (Blueprint $table) {
            $table->index('starts_at');
            $table->index('ends_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::table('holiday_opening_hours', function (Blueprint $table) {
            $table->dropIndex(['starts_at']);
            $table->dropIndex(['ends_at']);
        });
    }
}
