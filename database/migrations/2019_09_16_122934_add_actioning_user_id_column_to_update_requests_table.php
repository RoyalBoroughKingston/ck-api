<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddActioningUserIdColumnToUpdateRequestsTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::table('update_requests', function (Blueprint $table) {
            $table->uuid('actioning_user_id')->nullable()->after('user_id');
            $table->foreign('actioning_user_id')->references('id')->on('users');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::table('update_requests', function (Blueprint $table) {
            $table->dropForeign(['actioning_user_id']);
            $table->dropColumn('actioning_user_id');
        });
    }
}
