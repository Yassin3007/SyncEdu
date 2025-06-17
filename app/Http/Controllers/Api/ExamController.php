<?php
// app/Http/Controllers/ExamController.php
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\ExamRequest;
use App\Http\Resources\ExamResource;
use App\Models\Exam;
use App\Models\Question;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\ResourceCollection;
use Illuminate\Support\Facades\DB;

class ExamController extends Controller
{
    public function index(): ResourceCollection
    {
        $exams = Exam::with(['questions', 'creator:id,name'])
            ->withCount('questions')
            ->paginate(10);

        return ExamResource::collection($exams);
    }

    public function show(Exam $exam): ExamResource
    {
        $exam->load(['questions', 'creator:id,name']);

        return new ExamResource($exam);
    }

    public function store(ExamRequest $request)
    {
        try {
            DB::beginTransaction();

            $exam = Exam::create([
                'title' => $request->title,
                'description' => $request->description,
                'duration_minutes' => $request->duration_minutes,
                'start_time' => $request->start_time,
                'end_time' => $request->end_time,
                'created_by' => auth()->id(),
            ]);

            foreach ($request->questions as $index => $questionData) {
                $question = new Question([
                    'type' => $questionData['type'],
                    'question_text' => $questionData['question_text'],
                    'marks' => $questionData['marks'],
                    'correct_answer' => $questionData['correct_answer'],
                    'order' => $index + 1,
                ]);

                if ($questionData['type'] === 'multiple_choice') {
                    $question->options = $questionData['options'];
                } elseif ($questionData['type'] === 'true_false') {
                    $question->options = ['True', 'False'];
                }

                $exam->questions()->save($question);
            }

            $exam->calculateTotalMarks();

            DB::commit();

            return new ExamResource($exam);

        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'message' => 'Failed to create exam',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function update(ExamRequest $request, Exam $exam)
    {
        try {
            DB::beginTransaction();

            $exam->update([
                'title' => $request->title,
                'description' => $request->description,
                'duration_minutes' => $request->duration_minutes,
                'start_time' => $request->start_time,
                'end_time' => $request->end_time,
            ]);

            // Delete existing questions
            $exam->questions()->delete();

            // Create new questions
            foreach ($request->questions as $index => $questionData) {
                $question = new Question([
                    'type' => $questionData['type'],
                    'question_text' => $questionData['question_text'],
                    'marks' => $questionData['marks'],
                    'correct_answer' => $questionData['correct_answer'],
                    'order' => $index + 1,
                ]);

                if ($questionData['type'] === 'multiple_choice') {
                    $question->options = $questionData['options'];
                } elseif ($questionData['type'] === 'true_false') {
                    $question->options = ['True', 'False'];
                }

                $exam->questions()->save($question);
            }

            $exam->calculateTotalMarks();

            DB::commit();

            return new ExamResource($exam);

        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'message' => 'Failed to update exam',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function destroy(Exam $exam): JsonResponse
    {
        try {
            $exam->delete();

            return response()->json([
                'message' => 'Exam deleted successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to delete exam',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function toggleStatus(Exam $exam): JsonResponse
    {
        $exam->update(['is_active' => !$exam->is_active]);

        return response()->json([
            'message' => 'Exam status updated successfully',
            'exam' => $exam
        ]);
    }
}
