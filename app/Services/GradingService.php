<?php

// app/Services/GradingService.php
namespace App\Services;

use App\Models\Question;

class GradingService
{
    public function checkAnswer(Question $question, string $studentAnswer): bool
    {
        switch ($question->type) {
            case Question::TYPE_MULTIPLE_CHOICE:
            case Question::TYPE_TRUE_FALSE:
                return $this->checkExactMatch($question->correct_answer, $studentAnswer);

            case Question::TYPE_WRITTEN:
                return $this->checkWrittenAnswer($question->correct_answer, $studentAnswer);

            default:
                return false;
        }
    }

    private function checkExactMatch(string $correctAnswer, string $studentAnswer): bool
    {
        return strtolower(trim($correctAnswer)) === strtolower(trim($studentAnswer));
    }

    private function checkWrittenAnswer(string $correctAnswer, string $studentAnswer): bool
    {
        // Basic implementation - you can enhance this with fuzzy matching, keyword checking, etc.
        $correct = strtolower(trim($correctAnswer));
        $student = strtolower(trim($studentAnswer));

        // Exact match
        if ($correct === $student) {
            return true;
        }

        // Check if student answer contains all key words from correct answer
        $correctWords = array_filter(explode(' ', $correct));
        $studentWords = explode(' ', $student);

        $matchedWords = 0;
        foreach ($correctWords as $word) {
            if (in_array($word, $studentWords)) {
                $matchedWords++;
            }
        }

        // If 80% of key words match, consider it correct
        return ($matchedWords / count($correctWords)) >= 0.8;
    }

    public function calculatePartialCredit(Question $question, string $studentAnswer): float
    {
        if ($question->type !== Question::TYPE_WRITTEN) {
            return $this->checkAnswer($question, $studentAnswer) ? $question->marks : 0;
        }

        $correct = strtolower(trim($question->correct_answer));
        $student = strtolower(trim($studentAnswer));

        if (empty($student)) {
            return 0;
        }

        // Calculate similarity percentage
        $similarity = 0;
        similar_text($correct, $student, $similarity);

        // Award partial credit based on similarity
        if ($similarity >= 90) return $question->marks;
        if ($similarity >= 70) return $question->marks * 0.8;
        if ($similarity >= 50) return $question->marks * 0.6;
        if ($similarity >= 30) return $question->marks * 0.4;

        return 0;
    }
}
