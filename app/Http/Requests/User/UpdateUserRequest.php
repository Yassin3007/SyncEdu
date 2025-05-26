<?php

namespace App\Http\Requests\User;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateUserRequest extends FormRequest
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
        $userId = $this->route('user')->id;

        return [
            'name' => 'required|string|max:255',
            'email' => [
                'required',
                'string',
                'email',
                'max:255',
                Rule::unique('users', 'email')->ignore($userId)
            ],
            'password' => 'nullable|string|min:8|confirmed',
            'national_id' => [
                'nullable',
                'string',
                'max:255',
                Rule::unique('users', 'national_id')->ignore($userId)
            ],
            'phone' => 'nullable|string|max:255',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'active' => 'boolean',
            'salary' => 'nullable|integer|min:0',
            'roles' => 'required|array|min:1',
            'roles.*' => 'exists:roles,id',
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'name.required' => 'The name field is required.',
            'name.string' => 'The name must be a string.',
            'name.max' => 'The name may not be greater than 255 characters.',

            'email.required' => 'The email field is required.',
            'email.email' => 'The email must be a valid email address.',
            'email.unique' => 'This email address is already taken.',
            'email.max' => 'The email may not be greater than 255 characters.',

            'password.required' => 'The password field is required.',
            'password.min' => 'The password must be at least 8 characters.',
            'password.confirmed' => 'The password confirmation does not match.',

            'national_id.unique' => 'This national ID is already taken.',
            'national_id.max' => 'The national ID may not be greater than 255 characters.',

            'phone.max' => 'The phone may not be greater than 255 characters.',

            'start_date.date' => 'The start date must be a valid date.',
            'end_date.date' => 'The end date must be a valid date.',
            'end_date.after_or_equal' => 'The end date must be after or equal to the start date.',

            'active.boolean' => 'The active field must be true or false.',

            'salary.integer' => 'The salary must be an integer.',
            'salary.min' => 'The salary must be at least 0.',

            'roles.required' => 'Please select at least one role.',
            'roles.array' => 'The roles must be an array.',
            'roles.min' => 'Please select at least one role.',
            'roles.*.exists' => 'One or more selected roles are invalid.',
        ];
    }

    /**
     * Get custom attributes for validator errors.
     */
    public function attributes(): array
    {
        return [
            'national_id' => 'national ID',
            'start_date' => 'start date',
            'end_date' => 'end date',
        ];
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation(): void
    {
        // Convert active to boolean if it exists
        if ($this->has('active')) {
            $this->merge([
                'active' => $this->boolean('active')
            ]);
        }

        // Remove empty salary value and convert to null
        if ($this->salary === '' || $this->salary === null) {
            $this->merge([
                'salary' => null
            ]);
        }

        // Remove empty date values and convert to null
        if ($this->start_date === '') {
            $this->merge([
                'start_date' => null
            ]);
        }

        if ($this->end_date === '') {
            $this->merge([
                'end_date' => null
            ]);
        }

        // Remove empty national_id and phone values
        if ($this->national_id === '') {
            $this->merge([
                'national_id' => null
            ]);
        }

        if ($this->phone === '') {
            $this->merge([
                'phone' => null
            ]);
        }
    }
}
