<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddIndexesToOrganisationsTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::table('organisations', function (Blueprint $table) {
            $table->index('name');
            $table->index('created_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::table('organisations', function (Blueprint $table) {
            $table->dropIndex(['name']);
            $table->dropIndex(['created_at']);
        });
    }
}
