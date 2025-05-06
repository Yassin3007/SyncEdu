<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Stage extends Model
{
    protected $appends = ['name'] ;
    public function getNameAttribute(){
        return $this->{'name_' . app()->getLocale()};
    }
}
