<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RoleReportsPermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('role_reports_permissions')->insert([
            'role_id' => 1,
            'inventory_report' => 1,
            'cgt_summary' => 1,
            'nt_summary' => 1,
            'color_summary' => 1,
            'commulative_cgt' => 1,
            'commulative_nt' => 1,
            'customer_summary' => 1,
            'nexpac_report' => 1,
            'internal_report' => 1,
            'billing_report' => 1,
            'created_at' => now(),
            'updated_at' => now()
        ]);
    }
}
