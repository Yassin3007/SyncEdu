<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class GradesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $now = Carbon::now();

        // Elementary stage grades (stage_id = 1)
        $elementaryGrades = [
            [
                'name_en' => 'First Grade',
                'name_ar' => 'الصف الأول الابتدائي',
                'stage_id' => 1,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'name_en' => 'Second Grade',
                'name_ar' => 'الصف الثاني الابتدائي',
                'stage_id' => 1,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'name_en' => 'Third Grade',
                'name_ar' => 'الصف الثالث الابتدائي',
                'stage_id' => 1,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'name_en' => 'Fourth Grade',
                'name_ar' => 'الصف الرابع الابتدائي',
                'stage_id' => 1,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'name_en' => 'Fifth Grade',
                'name_ar' => 'الصف الخامس الابتدائي',
                'stage_id' => 1,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'name_en' => 'Sixth Grade',
                'name_ar' => 'الصف السادس الابتدائي',
                'stage_id' => 1,
                'created_at' => $now,
                'updated_at' => $now,
            ],
        ];

        // Intermediate stage grades (stage_id = 2)
        $intermediateGrades = [
            [
                'name_en' => 'Seventh Grade',
                'name_ar' => 'الصف الأول المتوسط',
                'stage_id' => 2,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'name_en' => 'Eighth Grade',
                'name_ar' => 'الصف الثاني المتوسط',
                'stage_id' => 2,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'name_en' => 'Ninth Grade',
                'name_ar' => 'الصف الثالث المتوسط',
                'stage_id' => 2,
                'created_at' => $now,
                'updated_at' => $now,
            ],
        ];

        // Secondary stage grades (stage_id = 3)
        $secondaryGrades = [
            [
                'name_en' => 'Tenth Grade',
                'name_ar' => 'الصف الأول الثانوي',
                'stage_id' => 3,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'name_en' => 'Eleventh Grade',
                'name_ar' => 'الصف الثاني الثانوي',
                'stage_id' => 3,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'name_en' => 'Twelfth Grade',
                'name_ar' => 'الصف الثالث الثانوي',
                'stage_id' => 3,
                'created_at' => $now,
                'updated_at' => $now,
            ],
        ];

        // University stage grades (stage_id = 4)
        $universityGrades = [
            [
                'name_en' => 'First Year',
                'name_ar' => 'السنة الأولى',
                'stage_id' => 4,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'name_en' => 'Second Year',
                'name_ar' => 'السنة الثانية',
                'stage_id' => 4,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'name_en' => 'Third Year',
                'name_ar' => 'السنة الثالثة',
                'stage_id' => 4,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'name_en' => 'Fourth Year',
                'name_ar' => 'السنة الرابعة',
                'stage_id' => 4,
                'created_at' => $now,
                'updated_at' => $now,
            ],
        ];

        // Postgraduate stage grades (stage_id = 5)
        $postgraduateGrades = [
            [
                'name_en' => 'Masters',
                'name_ar' => 'الماجستير',
                'stage_id' => 5,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'name_en' => 'Doctorate',
                'name_ar' => 'الدكتوراه',
                'stage_id' => 5,
                'created_at' => $now,
                'updated_at' => $now,
            ],
        ];

        // Merge all grades and insert them into the database
        $allGrades = array_merge(
            $elementaryGrades,
            $intermediateGrades,
            $secondaryGrades,
            $universityGrades,
            $postgraduateGrades
        );

        DB::table('grades')->insert($allGrades);
    }
}
