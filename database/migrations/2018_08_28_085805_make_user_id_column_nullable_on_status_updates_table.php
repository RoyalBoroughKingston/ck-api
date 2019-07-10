<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class MakeUserIdColumnNullableOnStatusUpdatesTable extends Migration
{
    /**
     * MakeUserIdColumnNullableOnStatusUpdatesTable constructor.
     */
    public function __construct()
    {
        register_enum_type();
    }

    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::table('status_updates', function (Blueprint $table) {
            $table->string('user_id', 36)->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::table('status_updates', function (Blueprint $table) {
            $table->string('user_id', 36)->nullable(false)->change();
        });
    }
}
