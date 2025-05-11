<?php

namespace App\Models;

use App\Filters\Filters;
use Illuminate\Database\Eloquent\Model;

class Teacher extends Model
{
    /**
     *
     */
    protected $fillable = [
        'name',
        'national_id',
        'phone',
        'division',
        'school',
    ];

    public function scopeFilter($query, Filters $filter)
    {
        return $filter->apply($query);
    }

    public function subjects()
    {
        return $this->belongsToMany(Subject::class);
    }

    public function grades()
    {
        return $this->belongsToMany(Grade::class);
    }

}
