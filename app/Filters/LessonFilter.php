<?php

namespace App\Filters;

class LessonFilter extends Filters
{
    protected $var_filters = [
        'teacher_id' , 'subject_id' , 'day' , 'time' , 'stage_id' , 'grade_id' , 'division_id' , 'day_from' , 'day_to'
    ];



    public function teacher_id($value)
    {
        return $this->builder->where('teacher_id', $value);
    }

    public function subject_id($value)
    {
        return $this->builder->where('subject_id', $value);
    }

    public function stage_id($value)
    {
        return $this->builder->where('stage_id', $value);
    }
    public function grade_id($value)
    {
        return $this->builder->where('grade_id', $value);

    }
    public function division_id($value)
    {
        return $this->builder->where('division_id', $value);

    }

    public function day_from($value){
        return $this->builder->whereDate('day','>=', $value);
    }

    public function day_to($value){
        return $this->builder->whereDate('day','<=', $value);
    }

//
//    public function test()
//    {
//        // Filter by teacher_id
//        if ($request->has('teacher_id')) {
//            $query->where('teacher_id', $request->teacher_id);
//        }
//
//        // Filter by subject_id
//        if ($request->has('subject_id')) {
//            $query->where('subject_id', $request->subject_id);
//        }
//
//        // Filter by stage_id
//        if ($request->has('stage_id')) {
//            $query->where('stage_id', $request->stage_id);
//        }
//
//        // Filter by grade_id
//        if ($request->has('grade_id')) {
//            $query->where('grade_id', $request->grade_id);
//        }
//
//        // Filter by division_id
//        if ($request->has('division_id')) {
//            $query->where('division_id', $request->division_id);
//        }
//
//        // Filter by date range
//        if ($request->has('start_date')) {
//            $query->where('day', '>=', $request->start_date);
//        }
//
//        if ($request->has('end_date')) {
//            $query->where('day', '<=', $request->end_date);
//        }
//
//        // Filter by status
//        if ($request->has('status')) {
//            $query->where('status', $request->status);
//        }
//    }




}
