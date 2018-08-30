<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class MakeVideoEmbedColumnVarcharOnServicesTable extends Migration
{
    /**
     * MakeVideoEmbedColumnVarcharOnServicesTable constructor.
     */
    public function __construct()
    {
        register_enum_type();
    }

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('services', function (Blueprint $table) {
            $table->string('video_embed')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('services', function (Blueprint $table) {
            $table->text('video_embed')->nullable()->change();
        });
    }
}
