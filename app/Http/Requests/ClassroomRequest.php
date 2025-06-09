<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ClassroomRequest extends FormRequest
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
            'code' => ['required', 'string', Rule::unique('classrooms', 'code'),
                function ($attribute, $value, $fail) {
                    if ($value == 'admin') {
                        $fail('The value is forbidden');
                    }
                }
            ],
            'name' => ['required', 'string', 'max:255'],
            'section' => ['nullable', 'string', 'max:255'],
            'subject' => ['nullable', 'string', 'max:255'],
            'room' => ['nullable', 'string', 'max:255'],
            'classroom' => [
                'nullable',
                'image',
                Rule::dimensions([
                    'min_width' => 600,
                    'min_height' => 300,
                ]),
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'The name field is required.',
            'cover.max' => 'Image size is greater than 1MB.',
        ];
    }
}
