<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateStatusUpdatesTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('status_updates', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->customForeignUuid('user_id', 'users');
            $table->customForeignUuid('referral_id', 'referrals');
            $table->enum('from', ['new', 'in_progress', 'completed', 'incompleted']);
            $table->enum('to', ['new', 'in_progress', 'completed', 'incompleted']);
            $table->text('comments')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::dropIfExists('status_updates');
    }
}
