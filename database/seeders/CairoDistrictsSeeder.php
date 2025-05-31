<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CairoDistrictsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $districts = [
            ['name_en' => 'Maadi', 'name_ar' => 'المعادي'],
            ['name_en' => 'Zamalek', 'name_ar' => 'الزمالك'],
            ['name_en' => 'Downtown Cairo', 'name_ar' => 'وسط البلد'],
            ['name_en' => 'New Cairo', 'name_ar' => 'القاهرة الجديدة'],
            ['name_en' => 'Heliopolis', 'name_ar' => 'مصر الجديدة'],
            ['name_en' => 'Nasr City', 'name_ar' => 'مدينة نصر'],
            ['name_en' => 'Dokki', 'name_ar' => 'الدقي'],
            ['name_en' => 'Agouza', 'name_ar' => 'العجوزة'],
            ['name_en' => 'Mohandessin', 'name_ar' => 'المهندسين'],
            ['name_en' => 'Giza', 'name_ar' => 'الجيزة'],
            ['name_en' => 'Garden City', 'name_ar' => 'جاردن سيتي'],
            ['name_en' => 'Islamic Cairo', 'name_ar' => 'القاهرة الإسلامية'],
            ['name_en' => 'Old Cairo', 'name_ar' => 'مصر القديمة'],
            ['name_en' => 'Shubra', 'name_ar' => 'شبرا'],
            ['name_en' => 'Abbassia', 'name_ar' => 'العباسية'],
            ['name_en' => 'Ain Shams', 'name_ar' => 'عين شمس'],
            ['name_en' => 'Matariya', 'name_ar' => 'المطرية'],
            ['name_en' => 'Zeitoun', 'name_ar' => 'الزيتون'],
            ['name_en' => 'Manshiyat Naser', 'name_ar' => 'منشية ناصر'],
            ['name_en' => 'Sayeda Zeinab', 'name_ar' => 'السيدة زينب'],
            ['name_en' => 'Bulaq', 'name_ar' => 'بولاق'],
            ['name_en' => 'Rod El Farag', 'name_ar' => 'روض الفرج'],
            ['name_en' => 'Shoubra El Kheima', 'name_ar' => 'شبرا الخيمة'],
            ['name_en' => 'Qalyub', 'name_ar' => 'قليوب'],
            ['name_en' => 'Hadayek El Kobba', 'name_ar' => 'حدائق القبة'],
            ['name_en' => 'Waily', 'name_ar' => 'الوايلي'],
            ['name_en' => 'Sahel', 'name_ar' => 'الساحل'],
            ['name_en' => 'Zawya El Hamra', 'name_ar' => 'الزاوية الحمراء'],
            ['name_en' => 'Khalifa', 'name_ar' => 'الخليفة'],
            ['name_en' => 'Mokattam', 'name_ar' => 'المقطم'],
            ['name_en' => 'Basatin', 'name_ar' => 'البساتين'],
            ['name_en' => 'Dar El Salam', 'name_ar' => 'دار السلام'],
            ['name_en' => 'Maasara', 'name_ar' => 'المعصرة'],
            ['name_en' => '15th of May City', 'name_ar' => 'مدينة 15 مايو'],
            ['name_en' => 'Hadayek October', 'name_ar' => 'حدائق أكتوبر'],
            ['name_en' => '6th of October City', 'name_ar' => 'مدينة 6 أكتوبر'],
            ['name_en' => 'Sheikh Zayed City', 'name_ar' => 'مدينة الشيخ زايد'],
            ['name_en' => 'El Rehab City', 'name_ar' => 'مدينة الرحاب'],
            ['name_en' => 'Tagammu El Khames', 'name_ar' => 'التجمع الخامس'],
            ['name_en' => 'New Administrative Capital', 'name_ar' => 'العاصمة الإدارية الجديدة'],
        ];

        foreach ($districts as $district) {
            DB::table('districts')->updateOrInsert(
                ['name_en' => $district['name_en']],
                [
                    'name_en' => $district['name_en'],
                    'name_ar' => $district['name_ar'],
                    'city_en' => 'Cairo',
                    'city_ar' => 'القاهرة',
                    'created_at' => now(),
                    'updated_at' => now(),
                ]
            );
        }
    }
}
