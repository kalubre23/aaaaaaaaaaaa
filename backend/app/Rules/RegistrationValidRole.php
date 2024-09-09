<?php

namespace App\Rules;

use App\Models\Role;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class RegistrationValidRole implements ValidationRule
{

    
    /**
     * Run the validation rule.
     *
     * @param  \Closure(string): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $allowedRoles = ['Student', 'Parent'];

        $role = Role::where('id', $value)
            ->whereIn('name', $allowedRoles)
            ->exists();

        if (!$role) {
            $fail('Invalid role. Valid roles are Student and Parent');
        }
    }
}
