<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddIndexesToUpdateRequestsTable extends Migration
{
    /**
     * Run the migrations.
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
