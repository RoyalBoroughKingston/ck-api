<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAuditsTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('audits', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->nullableForeignUuid('user_id', 'users');
            $table->enum('action', ['create', 'read', 'update', 'delete']);
            $table->string('description', 1000);
            $table->ipAddress('ip_address');
            $table->string('user_agent', 1000)->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::dropIfExists('audits');
    }
}
