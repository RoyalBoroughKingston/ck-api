<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateReferralsTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('referrals', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('service_id', 'services');
            $table->char('reference', 10)->unique();
            $table->enum('status', ['new', 'in_progress', 'completed', 'incompleted']);
            $table->text('name');
            $table->text('email')->nullable();
            $table->text('phone')->nullable();
            $table->text('other_contact')->nullable();
            $table->text('postcode_outward_code')->nullable();
            $table->text('comments')->nullable();
            $table->timestamp('referral_consented_at')->nullable();
            $table->timestamp('feedback_consented_at')->nullable();
            $table->text('referee_name')->nullable();
            $table->text('referee_email')->nullable();
            $table->text('referee_phone')->nullable();
            $table->nullableForeignUuid('organisation_taxonomy_id', 'taxonomies');
            $table->string('organisation')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::dropIfExists('referrals');
    }
}
