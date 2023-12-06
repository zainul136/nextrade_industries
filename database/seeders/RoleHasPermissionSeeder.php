<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RoleHasPermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('role_has_permissions')->insert([
            'role_id' => 1,
            'users' => 1,
            'roles' => 1,
            'warehouses' => 1,
            'customers' => 1,
            'suppliers' => 1,
            'cgt_gardes' => 1,
            'nt_grades' => 1,
            'colors' => 1,
            'product_types' => 1,
            'scan_in' => 1,
            'scan_out' => 1,
            'inventory' => 1,
            'orders' => 1,
            'reports' => 1,
            'nt_grade_column' => 1,
            'nt_price_column' => 1,
            'third_party_price_column' => 1,
            'created_at' => now(),
            'updated_at' => now()
        ]);
    }
}
