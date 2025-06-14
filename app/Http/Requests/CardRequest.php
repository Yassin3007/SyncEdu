<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CardRequest extends FormRequest
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
            'months_count' =>  'required|integer|min:1|max:12',
            'exception_type' => 'required',
            'discount_type'  =>  'nullable|in:percentage,amount',
            'discount'  =>  'nullable|numeric',
            'installments_count'  =>  'nullable|integer|min:1|max:12',
            'exemption_reason_id'  => 'nullable|integer|exists:exemption_reasons,id',
            'twin_id'  =>  'nullable|integer|exists:students,id',
            'price' =>  'required|numeric|min:1',
            'subjects' => 'required|array',
            'subjects.*' =>  'required|integer|exists:subjects,id',
        ];
    }
}
