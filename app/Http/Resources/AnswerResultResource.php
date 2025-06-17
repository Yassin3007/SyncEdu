<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AnswerResultResource extends JsonResource
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
            'question' => [
                'id' => $this->question->id,
                'question_text' => $this->question->question_text,
                'type' => $this->question->type,
                'marks' => $this->question->marks,
                'correct_answer' => $this->question->correct_answer,
                'options' => $this->question->options,
            ],
            'student_answer' => $this->student_answer,
            'is_correct' => $this->is_correct,
            'marks_obtained' => $this->marks_obtained,
        ];
    }
}
