<?php

namespace App\Http\Requests;

use Illuminate\Validation\Rule;
use Illuminate\Foundation\Http\FormRequest;

class StoreStudentRequest extends FormRequest
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
            // Student Bio
            'first_name' => ['required','string','max:255'],
            'last_name' => ['required','string','max:255'],
            'email' => ['nullable','email', 'unique:users,email'], // Optional for younger students
            'dob' => ['required','date'],
            'gender' => ['required', 'in:Male,Female'],
            'address' => ['nullable', 'string'],

            // Academic Info
            'admission_number' => [
                'required',
                'string',
                // Unique within the school context (or globally, depending on your pref)
                Rule::unique('student_profiles', 'admission_number')
            ],
            'class_level_id' => ['required', 'exists:class_levels,id'],
            'section_id' => ['required', 'exists:sections,id'],

            // Parent Info (We check parent email to see if they already exist)
            'parent_email' => ['required', 'email'],
            'parent_phone' => ['required', 'string'],
            'parent_name' => ['required', 'string'],
            'relationship' => ['required', 'string'], // Father, Mother, Guardian
        ];
    }
}
