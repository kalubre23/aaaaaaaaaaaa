<?php

namespace App\Http\Requests;

use App\Rules\SubjectValidRole;
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
            'name' => 'required|string',
            'teacher_id' => [new SubjectValidRole]
        ];
    }
    public function messages(): array
    {
        return [
            'name.required' => 'Subject name is required.',
            'teacher_id.required' => 'Teacher is required.'
        ];
    }
}
