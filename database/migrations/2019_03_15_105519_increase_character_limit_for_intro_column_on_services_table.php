<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Database\Migrations\Migration;

class IncreaseCharacterLimitForIntroColumnOnServicesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::statement('ALTER TABLE `services` MODIFY `intro` VARCHAR(300)');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::statement('ALTER TABLE `services` MODIFY `intro` VARCHAR(255)');
    }
}
