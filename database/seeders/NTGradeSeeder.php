<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class NTGradeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $ntGrades = array(
            [
                'grade_name' => 'First',
                'slug' => 'A',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'grade_name' => 'Second',
                'slug' => 'S',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'grade_name' => 'Third',
                'slug' => 'T',
                'created_at' => now(),
                'updated_at' => now()
            ]
        );

        foreach($ntGrades as $ntGrade) {
            DB::table('n_t_grades')->insert($ntGrade);
        }
    }
}
