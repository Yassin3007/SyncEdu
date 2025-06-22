<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\HomeworkRequest;
use App\Http\Resources\HomeworkResource;
use App\Models\Homework;
use App\Models\Lesson;
use App\Models\Question;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class HomeworkController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $homeworks = Homework::with(['questions', 'creator:id,name'])
            ->withCount('questions');
        if(Request()->has('paginate')){
            $perPage = Request()->input('per_page', 15);
            $homeworks = $homeworks->paginate($perPage);
        }

        $homeworks = $homeworks->get();

        return HomeworkResource::collection($homeworks);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(HomeworkRequest $request)
    {
        try {
            DB::beginTransaction();
            $lesson = Lesson::findOrFail($request->lesson_id);
            $next_lesson = $lesson->next_lesson;
            $start_time = Carbon::createFromFormat('Y-m-d H:i:s', $lesson->day . ' ' . $lesson->end);
            $end_time = Carbon::createFromFormat('Y-m-d H:i:s', $next_lesson->day . ' ' . $next_lesson->start);
            $homework = Homework::create([
                'title' => $request->title,
                'description' => $request->description,
                'start_time' => $start_time,
                'end_time' => $end_time,
                'created_by' => auth()->id(),
                'subject_id' => $request->subject_id,
                'lesson_id' => $request->lesson_id,
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

                $homework->questions()->save($question);
            }

            $homework->calculateTotalMarks();

            DB::commit();

            return new HomeworkResource($homework);

        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'message' => 'Failed to create homework',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Homework $homework): HomeworkResource
    {
        $homework->load(['questions', 'creator:id,name']);

        return new HomeworkResource($homework);
    }


    /**
     * Update the specified resource in storage.
     */
    public function update(HomeworkRequest $request, Homework $homework)
    {
        try {
            DB::beginTransaction();

            $homework->update([
                'title' => $request->title,
                'description' => $request->description,
                'duration_minutes' => $request->duration_minutes,
                'start_time' => $request->start_time,
                'end_time' => $request->end_time,
                'subject_id' => $request->subject_id,
                'lesson_id' => $request->lesson_id,
            ]);

            // Delete existing questions
            $homework->questions()->delete();

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

                $homework->questions()->save($question);
            }

            $homework->calculateTotalMarks();

            DB::commit();

            return new HomeworkResource($homework);

        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'message' => 'Failed to update homework',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Homework $homework)
    {
        try {
            $homework->delete();

            return response()->json([
                'message' => 'Homework deleted successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to delete Homework',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function toggleStatus(Homework $homework): JsonResponse
    {
        $homework->update(['is_active' => !$homework->is_active]);

        return response()->json([
            'message' => 'Homework status updated successfully',
            'homework' => new HomeworkResource($homework)
        ]);
    }
}
