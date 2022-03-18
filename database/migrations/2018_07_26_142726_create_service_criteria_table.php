<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateServiceCriteriaTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('service_criteria', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->customForeignUuid('service_id', 'services');
            $table->string('age_group')->nullable();
            $table->string('disability')->nullable();
            $table->string('employment')->nullable();
            $table->string('gender')->nullable();
            $table->string('housing')->nullable();
            $table->string('income')->nullable();
            $table->string('language')->nullable();
            $table->string('other')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::dropIfExists('service_criteria');
    }
}
