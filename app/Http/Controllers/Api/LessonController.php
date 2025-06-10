<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Lesson;
use App\Models\Teacher;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;

class LessonController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(Lesson $lesson)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Lesson $lesson)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Lesson $lesson)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Lesson $lesson)
    {
        //
    }

    function createLessonsForTeacher($teacherId)
    {
        // Get teacher with their schedule and lessons_count
        $teacher = Teacher::with('tables')->find($teacherId);

        if (!$teacher) {
            throw new Exception("Teacher not found with ID: {$teacherId}");
        }

        if ($teacher->tables->isEmpty()) {
            throw new Exception("No schedule found for teacher ID: {$teacherId}");
        }

        if (!$teacher->lessons_count || $teacher->lessons_count <= 0) {
            throw new Exception("Invalid lessons count for teacher ID: {$teacherId}");
        }

        // Map day names to Carbon constants
        $dayMapping = [
            'sunday' => Carbon::SUNDAY,
            'monday' => Carbon::MONDAY,
            'tuesday' => Carbon::TUESDAY,
            'wednesday' => Carbon::WEDNESDAY,
            'thursday' => Carbon::THURSDAY,
            'friday' => Carbon::FRIDAY,
            'saturday' => Carbon::SATURDAY,
        ];

        // Start from next Monday
        $startDate = Carbon::now()->next(Carbon::MONDAY);

        // Generate available dates based on teacher's schedule
        $availableDates = [];
        $weekCounter = 0;
        $targetLessons = $teacher->lessons_count;

        // Keep generating dates until we have enough slots for all lessons
        while (count($availableDates) < $targetLessons) {
            foreach ($teacher->tables as $schedule) {
                $dayName = strtolower($schedule->day);

                if (!isset($dayMapping[$dayName])) {
                    continue; // Skip invalid day names
                }

                $dayOfWeek = $dayMapping[$dayName];
                $currentDate = $startDate->copy();

                // Find the first occurrence of this day
                if ($currentDate->dayOfWeek !== $dayOfWeek) {
                    $currentDate = $currentDate->next($dayOfWeek);
                }

                // Add this week's date for this day
                $lessonDate = $currentDate->copy()->addWeeks($weekCounter);

                $availableDates[] = [
                    'date' => $lessonDate,
                    'schedule' => $schedule
                ];

                // Stop if we have enough slots
                if (count($availableDates) >= $targetLessons) {
                    break;
                }
            }
            $weekCounter++;
        }

        // Sort dates chronologically
        usort($availableDates, function($a, $b) {
            return $a['date']->timestamp - $b['date']->timestamp;
        });

        // Create lessons up to the specified count
        $lessonsCreated = 0;
        $dateIndex = 0;

        while ($lessonsCreated < $targetLessons && $dateIndex < count($availableDates)) {
            $dateInfo = $availableDates[$dateIndex];
            $schedule = $dateInfo['schedule'];

            // Check if lesson already exists for this date and teacher
            $existingLesson = Lesson::where('teacher_id', $teacherId)
                ->where('day', $dateInfo['date']->format('Y-m-d'))
                ->where('start', $schedule->start)
                ->first();

            if (!$existingLesson) {
                // Create the lesson
                Lesson::create([
                    'teacher_id' => $schedule->teacher_id,
                    'subject_id' => $schedule->subject_id,
                    'stage_id' => $schedule->stage_id,
                    'grade_id' => $schedule->grade_id,
                    'division_id' => $schedule->division_id,
                    'day' => $dateInfo['date']->format('Y-m-d'),
                    'start' => $schedule->start,
                    'end' => $schedule->end,
                    'price' => $schedule->price,
                    'teacher_rate' => $schedule->teacher_rate,
                    'status' => $schedule->status,
                ]);

                $lessonsCreated++;
            }

            $dateIndex++;
        }

        return $lessonsCreated;
    }

}
