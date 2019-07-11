<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Date;
use Illuminate\Support\Facades\DB;

class SeedDefaultReportTypeData extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        $now = Date::now();

        DB::table('report_types')->insert([
            'id' => uuid(),
            'name' => 'Commissioners Report',
            'created_at' => $now,
            'updated_at' => $now,
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        DB::table('report_types')->truncate();
    }
}
