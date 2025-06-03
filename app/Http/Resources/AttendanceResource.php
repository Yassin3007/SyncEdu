<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AttendanceResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'student' => $this->student ,
            'lesson'  => $this->lesson ,
            'attended' => $this->attended ,
            'qrcode_image' => $this->qr_code_url,
            'created_at' => $this->created_at
        ];
    }
}
