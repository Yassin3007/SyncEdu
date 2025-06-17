<?php
// app/Services/ExamService.php
namespace App\Services;

use App\Models\Exam;
use App\Models\Question;
use App\Models\ExamAttempt;
use App\Models\AnswerResult;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Collection;

class ExamService
{
    public function createExamWithQuestions(array $examData, array $questionsData): Exam
    {
        return DB::transaction(function () use ($examData, $questionsData) {
            $exam = Exam::create($examData);

            foreach ($questionsData as $index => $questionData) {
                $this->createQuestion($exam, $questionData, $index + 1);
            }

            $exam->calculateTotalMarks();
            return $exam->load('questions');
        });
    }

    public function updateExamWithQuestions(Exam $exam, array $examData, array $questionsData): Exam
    {
        return DB::transaction(function () use ($exam, $examData, $questionsData) {
            $exam->update($examData);

            // Delete existing questions
            $exam->questions()->delete();

            // Create new questions
            foreach ($questionsData as $index => $questionData) {
                $this->createQuestion($exam, $questionData, $index + 1);
            }

            $exam->calculateTotalMarks();
            return $exam->load('questions');
        });
    }

    private function createQuestion(Exam $exam, array $questionData, int $order): Question
    {
        $question = new Question([
            'type' => $questionData['type'],
            'question_text' => $questionData['question_text'],
            'marks' => $questionData['marks'],
            'correct_answer' => $questionData['correct_answer'],
            'order' => $order,
        ]);

        // Set options based on question type
        if ($questionData['type'] === Question::TYPE_MULTIPLE_CHOICE) {
            $question->options = $questionData['options'];
        } elseif ($questionData['type'] === Question::TYPE_TRUE_FALSE) {
            $question->options = ['True', 'False'];
        }

        return $exam->questions()->save($question);
    }

    public function startExamAttempt(Exam $exam, int $studentId): ExamAttempt
    {
        if (!$exam->isAvailable()) {
            throw new \Exception('This exam is not available');
        }

        $existingAttempt = ExamAttempt::where('exam_id', $exam->id)
            ->where('student_id', $studentId)
            ->first();

        if ($existingAttempt) {
            if ($existingAttempt->is_completed) {
                throw new \Exception('You have already completed this exam');
            }
            return $existingAttempt;
        }

        return ExamAttempt::create([
            'exam_id' => $exam->id,
            'student_id' => $studentId,
            'started_at' => now(),
            'answers' => []
        ]);
    }

    public function saveAnswer(ExamAttempt $attempt, int $questionId, string $answer): void
    {
        if ($attempt->is_completed) {
            throw new \Exception('Exam already completed');
        }

        if ($attempt->isTimeUp()) {
            throw new \Exception('Time is up');
        }

        $answers = $attempt->answers ?? [];
        $answers[$questionId] = $answer;
        $attempt->update(['answers' => $answers]);
    }

    public function submitExam(ExamAttempt $attempt): array
    {
        if ($attempt->is_completed) {
            throw new \Exception('Exam already completed');
        }

        return DB::transaction(function () use ($attempt) {
            $totalScore = 0;
            $correctAnswers = 0;
            $totalQuestions = $attempt->exam->questions->count();
            $answers = $attempt->answers ?? [];

            foreach ($attempt->exam->questions as $question) {
                $studentAnswer = $answers[$question->id] ?? '';
                $isCorrect = $question->checkAnswer($studentAnswer);
                $marksObtained = $isCorrect ? $question->marks : 0;

                AnswerResult::create([
                    'exam_attempt_id' => $attempt->id,
                    'question_id' => $question->id,
                    'student_answer' => $studentAnswer,
                    'is_correct' => $isCorrect,
                    'marks_obtained' => $marksObtained
                ]);

                $totalScore += $marksObtained;
                if ($isCorrect) $correctAnswers++;
            }

            $attempt->update([
                'submitted_at' => now(),
                'is_completed' => true,
                'total_score' => $totalScore
            ]);

            return [
                'total_score' => $totalScore,
                'total_marks' => $attempt->exam->total_marks,
                'percentage' => round(($totalScore / $attempt->exam->total_marks) * 100, 2),
                'correct_answers' => $correctAnswers,
                'total_questions' => $totalQuestions,
                'accuracy' => round(($correctAnswers / $totalQuestions) * 100, 2)
            ];
        });
    }

    public function getExamStatistics(Exam $exam): array
    {
        $attempts = $exam->attempts()->where('is_completed', true)->get();

        if ($attempts->isEmpty()) {
            return [
                'total_attempts' => 0,
                'average_score' => 0,
                'highest_score' => 0,
                'lowest_score' => 0,
                'pass_rate' => 0,
            ];
        }

        $scores = $attempts->pluck('total_score');
        $passThreshold = $exam->total_marks * 0.6; // 60% pass rate
        $passedCount = $attempts->where('total_score', '>=', $passThreshold)->count();

        return [
            'total_attempts' => $attempts->count(),
            'average_score' => round($scores->average(), 2),
            'highest_score' => $scores->max(),
            'lowest_score' => $scores->min(),
            'pass_rate' => round(($passedCount / $attempts->count()) * 100, 2),
            'score_distribution' => $this->getScoreDistribution($scores, $exam->total_marks),
        ];
    }

    private function getScoreDistribution(Collection $scores, float $totalMarks): array
    {
        $ranges = [
            'A (90-100%)' => ['min' => $totalMarks * 0.9, 'max' => $totalMarks],
            'B (80-89%)' => ['min' => $totalMarks * 0.8, 'max' => $totalMarks * 0.89],
            'C (70-79%)' => ['min' => $totalMarks * 0.7, 'max' => $totalMarks * 0.79],
            'D (60-69%)' => ['min' => $totalMarks * 0.6, 'max' => $totalMarks * 0.69],
            'F (0-59%)' => ['min' => 0, 'max' => $totalMarks * 0.59],
        ];

        $distribution = [];
        foreach ($ranges as $grade => $range) {
            $count = $scores->whereBetween('total_score', [$range['min'], $range['max']])->count();
            $distribution[$grade] = $count;
        }

        return $distribution;
    }

    public function getQuestionAnalysis(Question $question): array
    {
        $results = $question->answerResults()->with('examAttempt')->get();

        if ($results->isEmpty()) {
            return [
                'total_attempts' => 0,
                'correct_count' => 0,
                'incorrect_count' => 0,
                'accuracy_rate' => 0,
                'common_wrong_answers' => [],
            ];
        }

        $correctCount = $results->where('is_correct', true)->count();
        $incorrectAnswers = $results->where('is_correct', false)
            ->pluck('student_answer')
            ->countBy()
            ->sortDesc()
            ->take(5);

        return [
            'total_attempts' => $results->count(),
            'correct_count' => $correctCount,
            'incorrect_count' => $results->count() - $correctCount,
            'accuracy_rate' => round(($correctCount / $results->count()) * 100, 2),
            'common_wrong_answers' => $incorrectAnswers->toArray(),
        ];
    }
}

