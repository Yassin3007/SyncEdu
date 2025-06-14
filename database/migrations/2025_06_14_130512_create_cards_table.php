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
        Schema::create('cards', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained()->cascadeOnDelete();
            $table->integer('months_count')->default(0);
            $table->string('exception_type')->nullable();
            $table->string('discount_type')->nullable();
            $table->float('discount')->nullable();
            $table->integer('installments_count')->nullable();
            $table->foreignId('exemption_reason_id')->constrained()->cascadeOnDelete();
            $table->foreignId('twin_id')->constrained('students')->cascadeOnDelete();
            $table->boolean('status')->default(true)->comment('expired or not , handled from code');
            $table->float('price')->default(0);

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cards');
    }
};
