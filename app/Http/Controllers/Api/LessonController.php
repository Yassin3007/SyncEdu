<?php

namespace App\Http\Controllers\Api;

use App\Filters\LessonFilter;
use App\Http\Controllers\Controller;
use App\Http\Requests\LessonRequest;
use App\Http\Resources\LessonResource;
use App\Models\Lesson;
use Illuminate\Http\Request;

class LessonController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $filter = new LessonFilter(Request());
        $lessons = Lesson::query()->with(['teacher','subject'])->filter($filter) ;
        if(Request()->boolean('paginate')){
            $perPage = Request()->input('per_page', 15);
            $lessons = $lessons->paginate($perPage);
        }else{
            $lessons = $lessons->get();
        }
        return LessonResource::collection($lessons);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(LessonRequest $request)
    {
        $validated = $request->validated();
        $lesson = Lesson::query()->create($validated);
        return new LessonResource($lesson);

    }

    /**
     * Display the specified resource.
     */
    public function show(Lesson $lesson)
    {
        return new LessonResource($lesson);
    }


    /**
     * Update the specified resource in storage.
     */
    public function update(LessonRequest $request, Lesson $lesson)
    {
        $validated = $request->validated();
        $lesson->update($validated);
        return new LessonResource($lesson);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Lesson $lesson)
    {
        $lesson->delete();
        return apiResponse('api.success');
    }
}
