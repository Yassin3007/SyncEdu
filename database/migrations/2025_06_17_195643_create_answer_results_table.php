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
        Schema::create('answer_results', function (Blueprint $table) {
            $table->id();
            $table->foreignId('exam_attempt_id')->constrained('exam_attempts')->onDelete('cascade');
            $table->foreignId('question_id')->constrained('questions')->onDelete('cascade');
            $table->text('student_answer');
            $table->boolean('is_correct')->default(false);
            $table->decimal('marks_obtained', 8, 2)->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('answer_results');
    }
};
