<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ExamAttemptResource extends JsonResource
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
            'exam' => [
                'id' => $this->exam->id,
                'title' => $this->exam->title,
                'total_marks' => $this->exam->total_marks,
                'duration_minutes' => $this->exam->duration_minutes,
            ],
            'student' => $this->when($request->user()?->hasRole('admin'), [
                'id' => $this->student->id,
                'name' => $this->student->name,
                'email' => $this->student->email,
            ]),
            'started_at' => $this->started_at->format('Y-m-d H:i:s'),
            'submitted_at' => $this->submitted_at?->format('Y-m-d H:i:s'),
            'total_score' => $this->when($this->is_completed, $this->total_score),
            'percentage' => $this->when($this->is_completed, function () {
                return round(($this->total_score / $this->exam->total_marks) * 100, 2);
            }),
            'is_completed' => $this->is_completed,
            'remaining_time' => $this->when(!$this->is_completed, $this->remaining_time),
            'answer_results' => AnswerResultResource::collection($this->whenLoaded('answerResults')),
        ];
    }
}
