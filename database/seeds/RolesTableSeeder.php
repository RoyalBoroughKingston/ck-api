<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RolesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $now = now();

        DB::table('roles')->insert([
            'id' => uuid(),
            'name' => 'service_worker',
            'created_at' => $now,
            'updated_at' => $now,
        ]);

        DB::table('roles')->insert([
            'id' => uuid(),
            'name' => 'service_admin',
            'created_at' => $now,
            'updated_at' => $now,
        ]);

        DB::table('roles')->insert([
            'id' => uuid(),
            'name' => 'organisation_admin',
            'created_at' => $now,
            'updated_at' => $now,
        ]);

        DB::table('roles')->insert([
            'id' => uuid(),
            'name' => 'global_admin',
            'created_at' => $now,
            'updated_at' => $now,
        ]);

        DB::table('roles')->insert([
            'id' => uuid(),
            'name' => 'super_admin',
            'created_at' => $now,
            'updated_at' => $now,
        ]);
    }
}
