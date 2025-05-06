<?php

namespace Database\Seeders;

use Carbon\Carbon;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class StagesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
    {
        $now = Carbon::now();

        $stages = [
            [
                'name_en' => 'Elementary',
                'name_ar' => 'المرحلة الابتدائية',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'name_en' => 'Intermediate',
                'name_ar' => 'المرحلة المتوسطة',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'name_en' => 'Secondary',
                'name_ar' => 'المرحلة الثانوية',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'name_en' => 'University',
                'name_ar' => 'المرحلة الجامعية',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'name_en' => 'Postgraduate',
                'name_ar' => 'الدراسات العليا',
                'created_at' => $now,
                'updated_at' => $now,
            ],
        ];

        DB::table('stages')->insert($stages);
    }
}
