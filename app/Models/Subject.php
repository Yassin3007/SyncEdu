<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Subject extends Model
{
    protected $fillable = ['name_en', 'name_ar', 'grade_id', 'stage_id','division_id'];
    protected $appends = ['name'] ;

    public function getNameAttribute(){
        return $this->{'name_' . app()->getLocale()};
    }

    public function teachers()
    {
        return $this->belongsToMany(Teacher::class);
    }
}
