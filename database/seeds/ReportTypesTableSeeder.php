<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ReportTypesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $now = now();

        DB::table('report_types')->insert([
            'id' => uuid(),
            'name' => 'Commissioners Report',
            'created_at' => $now,
            'updated_at' => $now,
        ]);
    }
}
