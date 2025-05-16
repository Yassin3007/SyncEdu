<?php

namespace App\Filters;

class TeacherFilter extends Filters
{
    protected $var_filters = [
        'name',
        'national_id',
        'phone',
        'division_id',
        'school',
        'stage_id',
        'grade_id',
    ];

    public function name($value)
    {
        $this->builder->where(function ($query) use ($value) {
            $query->where('name_en', 'LIKE', '%' . $value . '%')->orWhere('name_ar', 'LIKE', '%' . $value . '%');
        });
    }

    public function national_id($national_id){
        return $this->builder->where('national_id', 'like', "%$national_id%");
    }
    public function phone($phone){
        return $this->builder->where('phone', 'like', "%$phone%");
    }
    public function division($value){
        $value = array($value);
        return $this->builder->whereHas('stages', function ($query) use ($value) {
            $query->whereHas('division', function ($q) use ($value) {
                $q->where('id', $value);
            });
        });
    }
    public function school($school){
        return $this->builder->where('school', 'like', "%$school%");
    }
    public function stage($value){
        $value = array($value);
        return $this->builder->whereHas('stages', function ($query) use ($value) {
            $query->where('id', $value);
        });
    }
    public function grade($value){
        $value = array($value);
        return $this->builder->whereHas('grades', function ($query) use ($value) {
            $query->where('id', $value);
        });
    }


}
