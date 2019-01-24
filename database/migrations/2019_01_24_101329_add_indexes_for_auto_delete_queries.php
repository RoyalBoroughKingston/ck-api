<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddIndexesForAutoDeleteQueries extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('referrals', function (Blueprint $table) {
            $table->index('updated_at');
        });

        Schema::table('audits', function (Blueprint $table) {
            $table->index('updated_at');
        });

        Schema::table('page_feedbacks', function (Blueprint $table) {
            $table->index('updated_at');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('referrals', function (Blueprint $table) {
            $table->dropIndex(['updated_at']);
        });

        Schema::table('audits', function (Blueprint $table) {
            $table->dropIndex(['updated_at']);
        });

        Schema::table('page_feedbacks', function (Blueprint $table) {
            $table->dropIndex(['updated_at']);
        });
    }
}
