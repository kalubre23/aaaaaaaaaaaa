<?php

namespace App\Http\Requests;

use App\Rules\OnlyAdminCanChangeRole;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateUserRequest extends FormRequest
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
        $user = $this->route('user');

        return [
            'email' => 'email',
            'password' => 'string|min:8',
            'username' => ['min:3',Rule::unique('users')->ignore($this->user()->id)],
            'name' => 'nullable|string',
            'surname' => 'nullable|string',
            'role_id' => ['nullable', 'integer'],
            'parent_id' => 'nullable|integer'
        ];
    }

    public function messages(): array
    {
        return [
            'email.email' => 'Email is not in a valid format.',
            'password.min' => 'Min password lenghth is 8.',
            'username.min' => 'Min username lenghth is 3.',
            'username.unique' => 'Username already exists.',
            'role_id.integer' => 'Role needs to be an integer.',
            'parent_id.integer' => 'Parent needs to be an integer.'
        ];
    }
}
