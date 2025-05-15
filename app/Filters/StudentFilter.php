<?php

namespace App\Filters;

class StudentFilter extends Filters
{
    protected $var_filters = [
        'name',
        'national_id',
        'guardian_number',
        'phone',
        'division',
        'school',
        'stage_id',
        'grade_id',
        'division_id',
        'subscription_type',
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
    public function guardian_number($guardian_number){
        return $this->builder->where('guardian_number', 'like', "%$guardian_number%");
    }
    public function phone($phone){
        return $this->builder->where('phone', 'like', "%$phone%");
    }
    public function division_id($value){
        return $this->builder->where('division_id', $value);
    }
    public function school($school){
        return $this->builder->where('school', 'like', "%$school%");
    }
    public function stage_id($value){
        return $this->builder->where('stage_id', $value);
    }
    public function grade_id($value){
        return $this->builder->where('grade_id', $value);
    }
    public function subscription_type($subscription_type){
        return $this->builder->where('subscription_type', 'like', "%$subscription_type%");
    }


}
