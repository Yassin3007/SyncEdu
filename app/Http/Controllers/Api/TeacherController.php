<?php

namespace App\Http\Controllers\Api;

use App\Filters\TeacherFilter;
use App\Http\Controllers\Controller;
use App\Http\Requests\TeacherRequest;
use App\Http\Resources\TeacherResource;
use App\Models\Teacher;
use Illuminate\Http\Request;

class TeacherController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $filter = new TeacherFilter(Request());
        if(Request()->has('paginate')){
            $perPage = Request()->input('per_page', 15);
            $teachers = Teacher::query()->filter($filter)->paginate($perPage);;
        }else{
            $teachers = Teacher::query()->filter($filter)->get();
        }
        return apiResponse('api.fetched', [TeacherResource::collection($teachers)]);
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
    public function store(TeacherRequest $request)
    {
        $validated = $request->validated();
        $teacher = Teacher::query()->create($validated);
        $teacher->subjects()->attach($validated['subjects']);
        $teacher->grades()->attach($validated['grades']);
        $teacher->stages()->attach($validated['stages']);

        return apiResponse('api.success');

    }

    /**
     * Display the specified resource.
     */
    public function show(Teacher $teacher)
    {
        return apiResponse('api.success',[new TeacherResource($teacher)]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Teacher $teacher)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(TeacherRequest $request, Teacher $teacher)
    {
        $teacher->update($request->validated());
        if($request->has('subjects')){
            $teacher->subjects()->sync($request->subjects);
        }
        if($request->has('grades')){
            $teacher->grades()->sync($request->grades);
        }
        return apiResponse('api.success');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Teacher $teacher)
    {
        $teacher->delete();
        return response()->json([
            'success' => true,
            'message' => 'api.success'
        ]);
    }
}
