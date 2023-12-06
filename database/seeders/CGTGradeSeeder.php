<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class CGTGradeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $cgtGrades = array(
            [
                'grade_name' => 'First',
                'slug' => '1',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'grade_name' => 'Second',
                'slug' => '2',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'grade_name' => 'Third',
                'slug' => '3',
                'created_at' => now(),
                'updated_at' => now()
            ]
        );

        foreach ($cgtGrades as $cgtGrade) {
            DB::table('c_g_t_grades')->insert($cgtGrade);
        }
    }
}
