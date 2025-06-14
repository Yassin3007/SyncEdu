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
        return StudentResource::collection($students);
    }



    /**
     * Store a newly created resource in storage.
     */
    public function store(StudentRequest $request)
    {
        $validated = $request->validated();
//        $id = null ;
        do{
            $validated['national_id'] = rand(1000000000,9999999999);
            $validated['password'] = $validated['national_id'];
        }while(Student::where('national_id',$validated['national_id'])->exists());
        $student = Student::query()->create($validated);
        return new StudentResource($student);

    }

    /**
     * Display the specified resource.
     */
    public function show(Student $student)
    {
        return new StudentResource($student);

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
        return new StudentResource($student);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Student $student)
    {
        $student->delete();
        return response()->json([
            'success' => true,
            'message' => 'api.success'
        ]);
    }

    public function bulkMove(Request $request)
    {
        $request->validate([
            'students' => 'required|array',
            'students.*' => 'required|exists:students,id',
            'stage_id' => 'required|exists:stages,id',
            'grade_id' => 'required|exists:grades,id',
        ]);
        Student::query()->whereIn('id', $request->students)->each(function ($student) use ($request) {
            $student->update([
                'stage_id' => $request->stage_id,
                'grade_id' => $request->grade_id,
            ]);
        });
        return response()->json([
            'success' => true,
            'message' => 'api.success'
        ]);
    }
}
