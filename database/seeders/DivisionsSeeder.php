<?php

namespace Database\Seeders;

use Carbon\Carbon;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

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
                'name_en' => 'scientific',
                'name_ar' => 'علمي',
                'stage_id' => 3,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'name_en' => 'literary',
                'name_ar' => 'ادبي',
                'stage_id' => 3,
                'created_at' => $now,
                'updated_at' => $now,
            ]
        ];

        DB::table('divisions')->insert($divisions);
    }
}
