<?php

namespace App\Http\Requests;

use Illuminate\Validation\Rule;
use Illuminate\Foundation\Http\FormRequest;

class UpdateSectionRequest extends FormRequest
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
                'exists:class_levels,id'
            ],
            'name' => [
                'required',
                'string',
                'max:50',
                // COMPOSITE UNIQUE CHECK (Update Version)
                Rule::unique('sections')
                    ->where('class_level_id', $this->class_level_id)
                    // The Magic Line: Ignore the ID of the section currently being updated
                    ->ignore($this->section)
            ],
            'capacity' => ['required','integer','min:1',],
            'is_active' => ['boolean']
        ];
    }
}
