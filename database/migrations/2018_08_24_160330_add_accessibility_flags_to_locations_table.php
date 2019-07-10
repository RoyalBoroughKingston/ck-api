<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddAccessibilityFlagsToLocationsTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::table('locations', function (Blueprint $table) {
            $table->boolean('has_wheelchair_access')->after('accessibility_info');
            $table->boolean('has_induction_loop')->after('has_wheelchair_access');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::table('locations', function (Blueprint $table) {
            $table->dropColumn('has_wheelchair_access');
            $table->dropColumn('has_induction_loop');
        });
    }
}
