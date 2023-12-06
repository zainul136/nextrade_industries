<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class SuperAdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('users')->insert([
            'full_name' => 'Superadmin Alphinex',
            'email' => 'superadmin@mail.com',
            'email_verified_at' => now(),
            'password' => bcrypt('admin123'),
            'role' => 1,
            'status' => 1,
            'created_at' => now(),
            'updated_at' => now()
        ]);
    }
}
