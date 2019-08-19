<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class IncreaseCharacterLimitForIntroColumnOnServicesTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        DB::statement('ALTER TABLE `services` MODIFY `intro` VARCHAR(300)');
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        DB::statement('ALTER TABLE `services` MODIFY `intro` VARCHAR(255)');
    }
}
