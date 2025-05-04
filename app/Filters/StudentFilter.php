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
        'stage',
        'grade',
        'subscription_type',
    ];

    public function name($name)
    {
        $this->builder->where('name', 'like', "%$name%");
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
    public function subscription_type($subscription_type){
        return $this->builder->where('subscription_type', 'like', "%$subscription_type%");
    }


}
