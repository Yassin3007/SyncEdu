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
        Schema::create('questions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('exam_id')->constrained('exams')->onDelete('cascade');
            $table->string('type'); // 'multiple_choice', 'written', 'true_false'
            $table->text('question_text');
            $table->decimal('marks', 8, 2);
            $table->json('options')->nullable(); // For multiple choice questions
            $table->text('correct_answer');
            $table->integer('order')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('questions');
    }
};
