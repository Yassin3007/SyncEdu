<?php

namespace App\Filters;

class LessonFilter extends Filters
{
    protected $var_filters = [
        'teacher_id' , 'subject_id' , 'day' , 'time' , 'stage_id' , 'grade_id' , 'division_id'
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


}
