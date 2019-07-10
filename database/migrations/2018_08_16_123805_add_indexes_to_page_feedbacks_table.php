<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddIndexesToPageFeedbacksTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::table('page_feedbacks', function (Blueprint $table) {
            $table->index('created_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::table('page_feedbacks', function (Blueprint $table) {
            $table->dropIndex(['created_at']);
        });
    }
}
