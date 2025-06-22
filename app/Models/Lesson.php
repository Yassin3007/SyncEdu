<?php

namespace App\Models;

use App\Filters\Filters;
use Illuminate\Database\Eloquent\Model;

class Lesson extends Model
{
    protected $guarded = [];

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

    public function getNextLessonAttribute()
    {
        return self::where('teacher_id', $this->teacher_id)
            ->where('day', '>', $this->day)
            ->orderBy('day')
            ->first();
    }
}
