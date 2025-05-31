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
        Schema::create('students', function (Blueprint $table) {
            $table->id();
            $table->string('name_en');
            $table->string('name_ar');
            $table->string('national_id')->nullable();
            $table->string('guardian_number')->nullable();
            $table->string('phone')->nullable();
            $table->string('division_id')->nullable();
            $table->string('school')->nullable();
            $table->foreignId('stage_id')->nullable()->constrained();
            $table->foreignId('grade_id')->nullable()->constrained();
            $table->foreignId('district_id')->nullable()->constrained();
            $table->string('subscription_type')->nullable();
            $table->decimal('wallet_balance')->default(0);
            $table->string('status')->default('active');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('students');
    }
};
