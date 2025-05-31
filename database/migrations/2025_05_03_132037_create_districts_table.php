<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('districts', function (Blueprint $table) {
            $table->id();
            $table->string('name_en');
            $table->string('name_ar');
            $table->string('city_en')->default('Cairo');
            $table->string('city_ar')->default('القاهرة');
            $table->timestamps();

            // Indexes for better performance
            $table->index('name_en');
            $table->index('name_ar');
            $table->index('city_en');
            $table->unique(['name_en', 'city_en']); // Prevent duplicate districts in same city
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('districts');
    }
};
