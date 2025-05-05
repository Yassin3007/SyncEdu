<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class TeacherRequest extends FormRequest
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
            'name' => 'required|string|max:255',
            'national_id' => 'nullable|string|max:255',
            'phone' => 'required|string|max:255',
            'division' => 'nullable|string|max:255',
            'school' => 'required|string|max:255',
//            'stage' => 'required|string|max:255',
//            'grade' => 'required|string|max:255',
        ];
    }
}
