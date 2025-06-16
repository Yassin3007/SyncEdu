<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class StudentResource extends JsonResource
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
            'guardian_number' => $this->guardian_number,
            'phone' => $this->phone,
            'division' => $this->division,
            'school' => $this->school,
            'stage' => $this->stage,
            'grade' => $this->grade,
            'subscription_type' => $this->subscription_type,
            'wallet' => $this->wallet,
            'qrcode_image' => $this->qr_code_url,
            'image'  =>$this->image ,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
