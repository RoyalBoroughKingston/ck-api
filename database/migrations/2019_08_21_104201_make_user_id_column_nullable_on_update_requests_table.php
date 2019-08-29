<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class MakeUserIdColumnNullableOnUpdateRequestsTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        DB::statement('ALTER TABLE `update_requests` MODIFY `user_id` CHAR(36) DEFAULT NULL;');
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        DB::statement('ALTER TABLE `update_requests` MODIFY `user_id` CHAR(36);');
    }
}
