<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class ColorSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $colors = array(
            [
                'name' => 'Red',
                'slug' => 'RD',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'name' => 'Black',
                'slug' => 'BK',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'name' => 'Pink',
                'slug' => 'PK',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'name' => 'Gold',
                'slug' => 'GD',
                'created_at' => now(),
                'updated_at' => now()
            ],
        );

        foreach ($colors as $color) {
            DB::table('colors')->insert($color);
        }
    }
}
