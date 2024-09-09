<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class MarkRequest extends FormRequest
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
            //'inserted_by' => 'required|integer',
            //'student_id' => 'required|integer',
            'value' => 'required|integer',
            //'subject_id' => 'required|integer'
        ];
    }

    public function messages(): array
    {
        return [
            // 'inserted_by.required' => 'Inserted by value is required.',
            // 'inserted_by.integer' => 'Inserted by must be an integer.',
            // 'student_id.required' => 'Student id value is required.',
            // 'student_id.integer' => 'Student id must be an integer.',
            'value.required' => 'Mark value is required.',
            'value.integer' => 'Mark must be an integer.',
            // 'subject_id.required' => 'Subject id value is required.',
            // 'subject_id.integer' => 'Subject id must be an integer.'
        ];
    }
}
