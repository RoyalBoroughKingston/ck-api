<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateServicesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('services', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('organisation_id', 'organisations');
            $table->nullableForeignUuid('logo_file_id', 'files');
            $table->string('name');
            $table->enum('status', ['active', 'inactive']);
            $table->string('intro');
            $table->text('description');
            $table->enum('wait_time', ['one_week', 'two_weeks', 'three_weeks', 'month', 'longer'])->nullable();
            $table->boolean('is_free');
            $table->string('fees_text')->nullable();
            $table->string('fees_url')->nullable();
            $table->string('testimonial')->nullable();
            $table->text('video_embed')->nullable();
            $table->string('url');
            $table->string('contact_name');
            $table->string('contact_phone');
            $table->string('contact_email');
            $table->json('accreditation_logos');
            $table->boolean('show_referral_disclaimer');
            $table->enum('referral_method', ['internal', 'external', 'none']);
            $table->string('referral_button_text')->nullable();
            $table->string('referral_email')->nullable();
            $table->string('referral_url')->nullable();
            $table->string('seo_title');
            $table->string('seo_description');
            $table->nullableForeignUuid('seo_image_file_id', 'files');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('services');
    }
}
