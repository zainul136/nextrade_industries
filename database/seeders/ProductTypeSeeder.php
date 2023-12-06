<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class ProductTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $productTypes = array(
            [
                'product_type' => 'Leather',
                'slug' => 'L',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'product_type' => 'Cotton',
                'slug' => 'C',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'product_type' => 'Glass',
                'slug' => 'G',
                'created_at' => now(),
                'updated_at' => now()
            ]
        );

        foreach($productTypes as $productType) {
            DB::table('product_types')->insert($productType);
        }
    }
}
