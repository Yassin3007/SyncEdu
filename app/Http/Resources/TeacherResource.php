<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TeacherResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'national_id' => $this->national_id,
            'phone' => $this->phone,
            'division' => $this->division,
            'school' => $this->school,
            'stage' => $this->stage,
            'grade' => $this->grade,
            'wallet' => $this->wallet,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
