<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddUniqueConstraintsToUserRolesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('user_roles', function (Blueprint $table) {
            $table->unique(['user_id', 'role_id', 'organisation_id', 'service_id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('user_roles', function (Blueprint $table) {
            $table->dropUnique(['user_id', 'role_id', 'organisation_id', 'service_id']);
        });
    }
}
