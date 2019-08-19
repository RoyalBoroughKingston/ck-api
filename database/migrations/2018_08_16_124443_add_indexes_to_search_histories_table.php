<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddIndexesToSearchHistoriesTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::table('search_histories', function (Blueprint $table) {
            $table->index('count');
            $table->index('created_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::table('search_histories', function (Blueprint $table) {
            $table->dropIndex(['count']);
            $table->dropIndex(['created_at']);
        });
    }
}
