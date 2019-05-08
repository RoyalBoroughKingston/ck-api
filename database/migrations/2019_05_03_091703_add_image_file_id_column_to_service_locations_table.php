<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddImageFileIdColumnToServiceLocationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('service_locations', function (Blueprint $table) {
            $table->uuid('image_file_id')->nullable()->after('location_id');
            $table->foreign('image_file_id')->references('id')->on('files');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('service_locations', function (Blueprint $table) {
            $table->dropForeign(['image_file_id']);
            $table->dropColumn('image_file_id');
        });
    }
}
