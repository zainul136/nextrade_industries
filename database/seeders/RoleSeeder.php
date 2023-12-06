<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $roles = array(
            [
                'name' => 'Super Admin',
                'created_at' => now(),
                'updated_at' => now()
            ]
        );

        foreach ($roles as $role) {
            DB::table('roles')->insert($role);
        }
    }
}
