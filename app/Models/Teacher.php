<?php

namespace App\Models;

use App\Filters\Filters;
use Illuminate\Database\Eloquent\Model;

class Teacher extends Model
{
    protected $fillable = [
        'name_en',
        'name_ar',
        'national_id',
        'phone',
        'division',
        'school',
        'lessons_count'
    ];
    protected $appends = ['name'] ;

    public function scopeFilter($query, Filters $filter)
    {
        return $filter->apply($query);
    }

    public function getNameAttribute(){
        return $this->{'name_' . app()->getLocale()};
    }

    public function subjects()
    {
        return $this->belongsToMany(Subject::class);
    }

    public function grades()
    {
        return $this->belongsToMany(Grade::class);
    }
    public function stages()
    {
        return $this->belongsToMany(Stage::class);
    }

    public function tables()
    {
        return $this->hasMany(Table::class);
    }

}
