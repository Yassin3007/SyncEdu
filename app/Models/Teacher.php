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
    ];
    protected $appends = ['name'] ;

    public function scopeFilter($query, Filters $filter)
    {
        return $filter->apply($query);
    }

    public function getNameAttribute(){
        return $this->{'name_' . app()->getLocale()};
    }
}
