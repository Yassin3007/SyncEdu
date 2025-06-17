<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class QuestionResource extends JsonResource
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
            'type' => $this->type,
            'question_text' => $this->question_text,
            'marks' => $this->marks,
            'options' => $this->when(
                in_array($this->type, ['multiple_choice', 'true_false']),
                $this->options
            ),
            'order' => $this->order,
            // Only show correct answer to admin/teacher, not students
//            'correct_answer' => $this->when(
//                $request->user()?->hasRole('admin') || $request->user()?->hasRole('teacher'),
//                $this->correct_answer
//            ),
        ];
    }
}
