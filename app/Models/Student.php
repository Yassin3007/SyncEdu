<?php

namespace App\Models;

use App\Filters\Filters;
use Illuminate\Database\Eloquent\Model;

class Student extends Model
{
    protected $fillable = [
        'name_en',
        'name_ar',
        'national_id',
        'guardian_number',
        'phone',
        'division',
        'school',
        'stage_id',
        'grade_id',
        'subscription_type',
        'wallet_balance'
    ];

    protected $appends = ['name'] ;

    public function scopeFilter($query, Filters $filter)
    {
        return $filter->apply($query);
    }

    public function getNameAttribute(){
        return $this->{'name_' . app()->getLocale()};
    }

    public function stage()
    {
        return $this->belongsTo(Stage::class);
    }

    public function grade(){
        return $this->belongsTo(Grade::class);
    }
}
