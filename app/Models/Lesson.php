<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Lesson extends Model
{
    protected $fillable = ['teacher_id', 'subject_id', 'day', 'start' , 'end' , 'stage_id','grade_id' , 'division_id'];

    public function teacher(){
        return $this->belongsTo(Teacher::class);
    }
    public function subject(){
        return $this->belongsTo(Subject::class);
    }

    public function stage(){
        return $this->belongsTo(Stage::class);
    }

    public function grade(){
        return $this->belongsTo(Grade::class);
    }
    public function division(){
        return $this->belongsTo(Division::class);
    }
}
