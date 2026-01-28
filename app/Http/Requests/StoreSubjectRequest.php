<?php

namespace App\Http\Requests;

use Illuminate\Validation\Rule;
use Illuminate\Foundation\Http\FormRequest;

class StoreSubjectRequest extends FormRequest
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

            'name' => ['required', 'string', 'max:255'],
            'code' => [
                'required',
                'string',
                'max:20',
                'alpha_num', // Optional: forces "MTH101" (no spaces/symbols)

                // SCOPED UNIQUENESS CHECK
                // Ensures "MTH101" is unique for THIS school only.
                Rule::unique('subjects', 'code')->where(function ($query) {
                    return $query->where('school_id', session('active_school'));
                })
            ],
            'description' => ['nullable', 'string', 'max:500'],
        ];
    }
}
