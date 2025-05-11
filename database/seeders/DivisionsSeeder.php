<?php

namespace Database\Seeders;

use Carbon\Carbon;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DivisionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
    {
        $now = Carbon::now();

        $divisions = [
            [
                'name_en' => 'School Education',
                'name_ar' => 'التعليم المدرسي',
                'stage_id' => 3,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'name_en' => 'Higher Education',
                'name_ar' => 'التعليم العالي',
                'stage_id' => 3,
                'created_at' => $now,
                'updated_at' => $now,
            ]
        ];

        DB::table('divisions')->insert($divisions);
    }
}
