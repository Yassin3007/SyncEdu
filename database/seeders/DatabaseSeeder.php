<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();
        $this->call([
            StagesSeeder::class,  // Make sure StagesSeeder runs first
            GradesSeeder::class,
            DivisionsSeeder::class,
            DivisionsSeeder::class,
            // Other seeders...
        ]);
        User::factory()->create([
            'name' => 'Test User',
            'email' => 'admin@admin.com',
            'password' => Hash::make('password'),
        ]);
    }
}
