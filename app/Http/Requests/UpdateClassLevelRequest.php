<?php

namespace App\Http\Requests;

use Illuminate\Validation\Rule;
use Illuminate\Foundation\Http\FormRequest;

class UpdateClassLevelRequest extends FormRequest
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
                // SCOPED UNIQUENESS CHECK (Update Version)
                Rule::unique('class_levels')
                    // 1. Ensure we stick to the current school scope
                    ->where(function ($query) {
                        return $query->where('school_id', session('active_school'));
                    })
                    // 2. Ignore the record currently being edited
                    // Laravel automatically resolves 'class_level' from the route: /class-levels/{class_level}
                    ->ignore($this-> route('classLevel'))
            ],
            'description' => ['nullable', 'string', 'max:500'],
            'is_active'   => ['boolean'],
        ];
    }
}
