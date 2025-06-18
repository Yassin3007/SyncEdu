<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ExamRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'duration_minutes' => 'nullable|integer|min:1',
            'start_time' => 'nullable|date',
            'end_time' => 'nullable|date|after:start_time',
            'subject_id' => 'required|exists:subjects,id',
            'questions' => 'required|array|min:1',
            'questions.*.type' => 'required|in:multiple_choice,written,true_false',
            'questions.*.question_text' => 'required|string',
            'questions.*.marks' => 'required|numeric|min:0.1',
            'questions.*.correct_answer' => 'required|string',
            'questions.*.options' => 'required_if:questions.*.type,multiple_choice|array|min:2',
        ];
    }
}
