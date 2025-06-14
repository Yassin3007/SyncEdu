<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Charge extends Model
{
    protected $guarded = [];

    public function subjects(){
        return $this->belongsToMany(Subject::class);
    }
}
