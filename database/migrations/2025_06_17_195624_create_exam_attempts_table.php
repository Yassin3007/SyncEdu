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
        Schema::create('exam_attempts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('exam_id')->constrained('exams')->onDelete('cascade');
            $table->foreignId('student_id')->constrained('students')->onDelete('cascade');
            $table->timestamp('started_at');
            $table->timestamp('submitted_at')->nullable();
            $table->decimal('total_score', 8, 2)->default(0);
            $table->json('answers'); // Store all answers
            $table->boolean('is_completed')->default(false);
            $table->timestamps();

            $table->unique(['exam_id', 'student_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('exam_attempts');
    }
};
