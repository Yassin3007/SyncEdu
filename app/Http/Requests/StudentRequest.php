<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Validator;

class StudentRequest extends FormRequest
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
            'name_en' => 'required|string|max:255',
            'name_ar' => 'required|string|max:255',
            'national_id' => 'nullable|string|max:255',
            'guardian_number' => 'nullable|string|max:255',
            'phone' => 'required|string|max:255',
            'division_id' => 'required|exists:divisions,id',
            'school' => 'required|string|max:255',
            'stage_id' => 'required|exists:stages,id',
            'grade_id' => 'required|exists:grades,id',
            'subscription_type' => 'required|string|max:255',
        ];
    }

//    public function after(): array
//    {
//        return [
//            function (Validator $validator) {
//                if ($this->somethingElseIsInvalid()) {
//                    $validator->errors()->add(
//                        'field',
//                        'Something is wrong with this field!'
//                    );
//                }
//            }
//        ];
//    }
}
