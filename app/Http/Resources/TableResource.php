<?php

namespace App\Http\Resources;

use App\Models\Table;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TableResource extends JsonResource
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
            'stage'            => $this ->stage ,
            'grade'            => $this ->grade ,
            'division'         => $this ->division
        ];    }
}
