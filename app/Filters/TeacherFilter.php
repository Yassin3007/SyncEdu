<?php

namespace App\Filters;

class TeacherFilter extends Filters
{
    protected $var_filters = [
        'name',
        'national_id',
        'phone',
        'division',
        'school',
        'stage',
        'grade',
    ];

    public function name($name)
    {
        $this->builder->where('name', 'like', "%$name%");
    }

    public function national_id($national_id){
        return $this->builder->where('national_id', 'like', "%$national_id%");
    }
    public function phone($phone){
        return $this->builder->where('phone', 'like', "%$phone%");
    }
    public function division($division){
        return $this->builder->where('division', 'like', "%$division%");
    }
    public function school($school){
        return $this->builder->where('school', 'like', "%$school%");
    }
    public function stage($stage){
        return $this->builder->where('stage', 'like', "%$stage%");
    }
    public function grade($grade){
        return $this->builder->where('grade', 'like', "%$grade%");
    }


}
