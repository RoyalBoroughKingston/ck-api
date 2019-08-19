<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Date;
use Illuminate\Support\Facades\DB;

class SeedDefaultRoleData extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        $now = Date::now();

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

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        DB::table('roles')->truncate();
    }
}
