<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreUserRequest extends FormRequest
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
            'email' => 'required|email',
            'password' => 'required|string|min:8',
            'username' => 'required|string|unique:users,username|min:3',
            'name' => 'required|string',
            'surname' => 'required|string',
            'role_id' => ['required', 'integer'],
            'parent_id' => 'nullable|integer'
        ];
    }

    public function messages(): array
    {
        return [
            'email.required' => 'Email is required.',
            'email.email' => 'Email is not in a valid format.',
            'password.required' => 'Password is required.',
            'password.min' => 'Min password lenghth is 8.',
            'username.required' => 'Username is required.',
            'username.min' => 'Min username lenghth is 3.',
            'username.unique' => 'Username already exists.',
            'name.required' => 'Name is required.',
            'surname.required' => 'Surname is required.',
            'role_id.required' => 'Role is required.',
            'role_id.integer' => 'Role needs to be an integer.',
            'parent_id.integer' => 'Parent needs to be an integer.'
        ];
    }
}
