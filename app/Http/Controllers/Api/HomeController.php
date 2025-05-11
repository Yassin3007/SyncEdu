<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Grade;
use App\Models\Stage;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function getStages(){
        $stages = Stage::all();
        return apiResponse('api.fetched', $stages->toArray());
    }
    public function getGrades($id){
        $grades = Grade::query()->where('stage_id', $id)->get();
        return apiResponse('api.fetched', $grades->toArray());
    }

}
