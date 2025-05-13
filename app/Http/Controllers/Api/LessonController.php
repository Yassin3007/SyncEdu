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
        if(Request()->has('paginate')){
            $perPage = Request()->input('per_page', 15);
            $lessons = Lesson::query()->with(['teacher','subject'])->filter($filter)->paginate($perPage);
        }else{
            $lessons = Lesson::query()->with(['teacher','subject'])->filter($filter)->get();
        }
        return apiResponse('api.fetched', [LessonResource::collection($lessons)]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(LessonRequest $request)
    {
        $validated = $request->validated();
        Lesson::query()->create($validated);
        return apiResponse('api.success');

    }

    /**
     * Display the specified resource.
     */
    public function show(Lesson $lesson)
    {
        //
    }


    /**
     * Update the specified resource in storage.
     */
    public function update(LessonRequest $request, Lesson $lesson)
    {
        $validated = $request->validated();
        $lesson->update($validated);
        return apiResponse('api.success');
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
