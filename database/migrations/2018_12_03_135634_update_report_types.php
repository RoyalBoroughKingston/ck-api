<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class UpdateReportTypes extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::table('report_types')->delete();

        $now = now();

        DB::table('report_types')->insert([
            'id' => uuid(),
            'name' => 'Users Export',
            'created_at' => $now,
            'updated_at' => $now,
        ]);

        DB::table('report_types')->insert([
            'id' => uuid(),
            'name' => 'Services Export',
            'created_at' => $now,
            'updated_at' => $now,
        ]);

        DB::table('report_types')->insert([
            'id' => uuid(),
            'name' => 'Organisations Export',
            'created_at' => $now,
            'updated_at' => $now,
        ]);

        DB::table('report_types')->insert([
            'id' => uuid(),
            'name' => 'Locations Export',
            'created_at' => $now,
            'updated_at' => $now,
        ]);

        DB::table('report_types')->insert([
            'id' => uuid(),
            'name' => 'Referrals Export',
            'created_at' => $now,
            'updated_at' => $now,
        ]);

        DB::table('report_types')->insert([
            'id' => uuid(),
            'name' => 'Feedback Export',
            'created_at' => $now,
            'updated_at' => $now,
        ]);

        DB::table('report_types')->insert([
            'id' => uuid(),
            'name' => 'Audit Logs Export',
            'created_at' => $now,
            'updated_at' => $now,
        ]);

        DB::table('report_types')->insert([
            'id' => uuid(),
            'name' => 'Search Histories Export',
            'created_at' => $now,
            'updated_at' => $now,
        ]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::table('report_types')->delete();

        $now = now();

        DB::table('report_types')->insert([
            'id' => uuid(),
            'name' => 'Commissioners Report',
            'created_at' => $now,
            'updated_at' => $now,
        ]);
    }
}
