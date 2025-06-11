<?php

namespace App\Http\Resources;

use App\Models\Table;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class LessonResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id'               => $this->id ,
            'teacherName'      => $this->teacher->name,
            'teacherImage'     => $this->teacher->image,
            'subject'          => $this->subject->name,
            'start'            => $this->start ,
            'end'              => $this->end ,
            'day'              => $this->day ,
            'day_name'         => Carbon::parse($this->day)->dayName  ,
            'stage'            => $this ->stage ,
            'grade'            => $this ->grade ,
            'division'         => $this ->division
        ];
    }
}
