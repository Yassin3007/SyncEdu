<?php

namespace App\Models;

use App\Filters\Filters;
use Illuminate\Database\Eloquent\Model;

class Student extends Model
{
    protected $fillable = [
        'name',
        'national_id',
        'guardian_number',
        'phone',
        'division',
        'school',
        'stage',
        'grade',
        'subscription_type',
        'wallet_balance'
    ];

    public function scopeFilter($query, Filters $filter)
    {
        return $filter->apply($query);
    }

}
