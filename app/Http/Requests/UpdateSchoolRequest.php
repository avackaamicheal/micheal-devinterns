<?php

namespace App\Http\Requests;

use Illuminate\Validation\Rule;
use Illuminate\Foundation\Http\FormRequest;

class UpdateSchoolRequest extends FormRequest
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

        $school = $this->route('school');
        
        return [
            'name'           => ['required', 'string', 'max:255'],
            'email'          => [
                'required',
                'email',
                Rule::unique('schools')->ignore($school->id)
            ],
            'address'        => ['required', 'string', 'max:255'],
            'principal_name' => ['required', 'string', 'max:255'],
            'phone_number'   => ['required', 'string'],
        ];

    }
}
