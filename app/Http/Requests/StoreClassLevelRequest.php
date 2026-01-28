<?php

namespace App\Http\Requests;

use Illuminate\Validation\Rule;
use Illuminate\Foundation\Http\FormRequest;

class StoreClassLevelRequest extends FormRequest
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
            'name' => [
                'required',
                'string',
                'max:255',
                // SCOPED UNIQUENESS CHECK
                // Validate that 'name' is unique ONLY where 'school_id' matches the current session.
                Rule::unique('class_levels')->where(function ($query) {
                    return $query->where('school_id', session('active_school'));
                })
            ],
            'description' => ['nullable', 'string', 'max:500'],
        ];

    }
}
