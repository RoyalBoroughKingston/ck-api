<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddIndexesToStatusUpdatesTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::table('status_updates', function (Blueprint $table) {
            $table->index('created_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::table('status_updates', function (Blueprint $table) {
            $table->dropIndex(['created_at']);
        });
    }
}
