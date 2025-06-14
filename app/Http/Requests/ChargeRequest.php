<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ChargeRequest extends FormRequest
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
            'student_id' => 'required|integer|exists:students,id',
            'type' => 'required' ,
            'lessons_count' => 'required|integer|min:1',
            'from_date' => 'required|date|after_or_equal:today',
            'to_date' => 'required|date|after_or_equal:from_date',
            'discount_type' => 'required|in:percentage,amount',
            'discount' => 'required|numeric|min:0',
            'price' => 'required|numeric|min:0',
            'subjects' => 'required|array',
            'subjects.*' =>  'required|integer|exists:subjects,id',
        ];
    }
}
