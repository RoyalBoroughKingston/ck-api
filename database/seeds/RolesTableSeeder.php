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
            'name' => 'Service Worker',
            'created_at' => $now,
            'updated_at' => $now,
        ]);

        DB::table('roles')->insert([
            'id' => uuid(),
            'name' => 'Service Admin',
            'created_at' => $now,
            'updated_at' => $now,
        ]);

        DB::table('roles')->insert([
            'id' => uuid(),
            'name' => 'Organisation Admin',
            'created_at' => $now,
            'updated_at' => $now,
        ]);

        DB::table('roles')->insert([
            'id' => uuid(),
            'name' => 'Global Admin',
            'created_at' => $now,
            'updated_at' => $now,
        ]);

        DB::table('roles')->insert([
            'id' => uuid(),
            'name' => 'Super Admin',
            'created_at' => $now,
            'updated_at' => $now,
        ]);
    }
}
