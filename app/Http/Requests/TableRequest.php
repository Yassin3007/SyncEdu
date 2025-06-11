<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class TableRequest extends FormRequest
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
            'teacher_id'  => 'required|exists:teachers,id',
            'subject_id'  => 'required|exists:subjects,id',
            'stage_id'    => 'required|exists:stages,id',
            'grade_id'    => 'required|exists:grades,id',
            'division_id' => 'required|exists:divisions,id',
            'day'         => 'required',
            'start'       => 'required',
            'end'         => 'required',
        ];
    }
}
