<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddOauthClientIdColumnToAuditsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('audits', function (Blueprint $table) {
            $table->unsignedInteger('oauth_client_id')->nullable()->after('user_id');
            $table->foreign('oauth_client_id')->references('id')->on('oauth_clients');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('audits', function (Blueprint $table) {
            $table->dropForeign(['oauth_client_id']);
            $table->dropColumn('oauth_client_id');
        });
    }
}
