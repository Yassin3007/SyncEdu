<?php

namespace App\Models;

use App\Filters\Filters;
use Illuminate\Database\Eloquent\Model;

class Table extends Model
{
    protected $fillable = ['teacher_id', 'subject_id', 'day', 'start' , 'end' , 'stage_id','grade_id' , 'division_id'];

    const DAYS = [
        '1' => 'SunDay',
        '2' => 'MonDay',
        '3' => 'TuesDay',
        '4' => 'WednesDay',
        '5' => 'ThursDay',
        '6' => 'FriDay',
        '7' => 'SaturDay',
    ];
    public function scopeFilter($query, Filters $filter)
    {
        return $filter->apply($query);
    }
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
