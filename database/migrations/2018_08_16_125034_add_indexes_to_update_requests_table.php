<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddIndexesToUpdateRequestsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('update_requests', function (Blueprint $table) {
            $table->index('created_at');
            $table->index('approved_at');
            $table->index('deleted_at');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('update_requests', function (Blueprint $table) {
            $table->dropIndex(['created_at']);
            $table->dropIndex(['approved_at']);
            $table->dropIndex(['deleted_at']);
        });
    }
}
