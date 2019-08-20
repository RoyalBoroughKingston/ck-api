<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Database\Migrations\Migration;

class MakeUpdateableIdColumnNullableOnUpdateRequestsTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        DB::statement('ALTER TABLE `update_requests` MODIFY `updateable_id` CHAR(36) DEFAULT NULL;');
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        DB::statement('ALTER TABLE `update_requests` MODIFY `updateable_id` CHAR(36);');
    }
}
