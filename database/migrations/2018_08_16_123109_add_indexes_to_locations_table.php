<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddIndexesToLocationsTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::table('locations', function (Blueprint $table) {
            $table->index('address_line_1');
            $table->index('address_line_2');
            $table->index('address_line_3');
            $table->index('city');
            $table->index('county');
            $table->index('postcode');
            $table->index('country');
            $table->index('created_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::table('locations', function (Blueprint $table) {
            $table->dropIndex(['address_line_1']);
            $table->dropIndex(['address_line_2']);
            $table->dropIndex(['address_line_3']);
            $table->dropIndex(['city']);
            $table->dropIndex(['county']);
            $table->dropIndex(['postcode']);
            $table->dropIndex(['country']);
            $table->dropIndex(['created_at']);
        });
    }
}
