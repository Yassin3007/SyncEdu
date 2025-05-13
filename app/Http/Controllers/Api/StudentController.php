<?php

namespace App\Http\Controllers\Api;

use App\Filters\StudentFilter;
use App\Http\Controllers\Controller;
use App\Http\Requests\StudentRequest;
use App\Http\Resources\StudentResource;
use App\Models\Student;
use Illuminate\Http\Request;

class StudentController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $filter = new StudentFilter(Request());
        if(Request()->has('paginate')){
            $perPage = Request()->input('per_page', 15);
            $students = Student::query()->filter($filter)->paginate($perPage);
        }else{
            $students = Student::query()->filter($filter)->get();
        }
        return apiResponse('api.fetched', [StudentResource::collection($students)]);
    }



    /**
     * Store a newly created resource in storage.
     */
    public function store(StudentRequest $request)
    {
        $validated = $request->validated();
        Student::query()->create($validated);
        return apiResponse('api.success');

    }

    /**
     * Display the specified resource.
     */
    public function show(Student $student)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Student $student)
    {

    }

    /**
     * Update the specified resource in storage.
     */
    public function update(StudentRequest $request, Student $student)
    {
        $student->update($request->validated());
        return apiResponse('api.success');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Student $student)
    {
        $student->delete();
        return apiResponse('api.success');
    }
}
