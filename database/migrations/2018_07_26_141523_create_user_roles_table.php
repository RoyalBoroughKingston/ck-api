<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUserRolesTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('user_roles', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->customForeignUuid('user_id', 'users');
            $table->customForeignUuid('role_id', 'roles');
            $table->nullableForeignUuid('organisation_id', 'organisations');
            $table->nullableForeignUuid('service_id', 'services');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::dropIfExists('user_roles');
    }
}
