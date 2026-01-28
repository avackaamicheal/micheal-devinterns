<?php

namespace App\Http\Requests;

use Illuminate\Validation\Rule;
use Illuminate\Foundation\Http\FormRequest;

class StoreSectionRequest extends FormRequest
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
            'class_level_id' => [
                'required',
                'exists:class_levels,id' // Basic check
            ],
            'name' => [
                'required',
                'string',
                'max:50',
                // COMPOSITE UNIQUE CHECK:
                // "For this specific Class Level, the Name must be unique."
                Rule::unique('sections')
                    ->where('class_level_id', $this->class_level_id)
            ],
            'capacity' => ['required','integer', 'min:1',]
        ];
    }
}
