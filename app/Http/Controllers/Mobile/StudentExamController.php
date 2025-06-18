<?php

namespace App\Http\Controllers\Mobile;

use App\Http\Controllers\Controller;
use App\Http\Resources\ExamResource;
use App\Models\Exam;
use App\Models\ExamAttempt;
use App\Models\AnswerResult;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class StudentExamController extends Controller
{
    public function availableExams()
    {
        $student = auth('student')->user();
        $exams = Exam::where('is_active', true)
            ->where(function ($query) {
                $query->whereNull('start_time')
                    ->orWhere('start_time', '<=', now());
            })
            ->where(function ($query) {
                $query->whereNull('end_time')
                    ->orWhere('end_time', '>=', now());
            })
            ->whereHas('subject',function ($query) use ($student) {
                $query->where('stage_id',$student->stage_id)->where('grade_id',$student->grade_id)->where('division_id',$student->division_id);
            })
            ->withCount('questions')
            ->with('creator:id,name')
            ->get();

        return ExamResource::collection($exams);
    }

    public function startExam(Request $request, Exam $exam)
    {
        if (!$exam->isAvailable()) {
            return response()->json([
                'message' => 'This exam is not available'
            ], 403);
        }
        // Check if student already attempted
        $existingAttempt = ExamAttempt::where('exam_id', $exam->id)
            ->where('student_id', auth('student')->id())
            ->first();

        if ($existingAttempt) {
            if ($existingAttempt->is_completed) {
                return response()->json([
                    'message' => 'You have already completed this exam'
                ], 403);
            }

            // Return existing attempt if not completed
            return response()->json([
                'message' => 'Exam resumed',
                'attempt' => $existingAttempt->load(['exam.questions']),
                'remaining_time' => $existingAttempt->remaining_time
            ]);
        }

        try {
            $attempt = ExamAttempt::create([
                'exam_id' => $exam->id,
                'student_id' => auth('student')->id(),
                'started_at' => now(),
                'answers' => []
            ]);

            return response()->json([
                'message' => 'Exam started successfully',
                'attempt' => $attempt->load(['exam.questions']),
                'remaining_time' => $attempt->remaining_time
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to start exam',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function submitAnswer(Request $request, ExamAttempt $attempt): JsonResponse
    {
        if ($attempt->student_id !== auth()->id()) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        if ($attempt->is_completed) {
            return response()->json(['message' => 'Exam already completed'], 403);
        }

        if ($attempt->isTimeUp()) {
            return response()->json(['message' => 'Time is up'], 403);
        }

        $validator = Validator::make($request->all(), [
            'question_id' => 'required|exists:questions,id',
            'answer' => 'required|string'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $question = $attempt->exam->questions()->findOrFail($request->question_id);

            // Update answers array
            $answers = $attempt->answers ?? [];
            $answers[$request->question_id] = $request->answer;
            $attempt->update(['answers' => $answers]);

            return response()->json([
                'message' => 'Answer saved successfully',
                'remaining_time' => $attempt->remaining_time
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to save answer',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function submitExam(ExamAttempt $attempt): JsonResponse
    {
        if ($attempt->student_id !== auth()->id()) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        if ($attempt->is_completed) {
            return response()->json(['message' => 'Exam already completed'], 403);
        }

        try {
            DB::beginTransaction();

            $totalScore = 0;
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
            }

            $attempt->update([
                'submitted_at' => now(),
                'is_completed' => true,
                'total_score' => $totalScore
            ]);

            DB::commit();

            return response()->json([
                'message' => 'Exam submitted successfully',
                'total_score' => $totalScore,
                'total_marks' => $attempt->exam->total_marks,
                'percentage' => round(($totalScore / $attempt->exam->total_marks) * 100, 2)
            ]);

        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'message' => 'Failed to submit exam',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function examResults(ExamAttempt $attempt): JsonResponse
    {
        if ($attempt->student_id !== auth()->id()) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        if (!$attempt->is_completed) {
            return response()->json(['message' => 'Exam not completed yet'], 403);
        }

        $attempt->load([
            'exam:id,title,total_marks',
            'answerResults.question:id,question_text,type,marks'
        ]);

        return response()->json([
            'attempt' => $attempt,
            'percentage' => round(($attempt->total_score / $attempt->exam->total_marks) * 100, 2)
        ]);
    }

    public function myAttempts(): JsonResponse
    {
        $attempts = ExamAttempt::where('student_id', auth()->id())
            ->with(['exam:id,title,total_marks'])
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return response()->json($attempts);
    }
}
