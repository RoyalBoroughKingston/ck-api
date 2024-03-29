<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSocialMediasTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('social_medias', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->customForeignUuid('service_id', 'services');
            $table->enum('type', ['twitter', 'facebook', 'instagram', 'youtube', 'other']);
            $table->string('url');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::dropIfExists('social_medias');
    }
}
