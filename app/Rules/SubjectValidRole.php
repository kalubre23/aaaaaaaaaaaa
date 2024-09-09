<?php

namespace App\Rules;

use App\Models\Role;
use App\Models\User;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class SubjectValidRole implements ValidationRule
{
    /**
     * Run the validation rule.
     *
     * @param  \Closure(string): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $allowedRoles = ['Teacher'];

        $user = User::with('role')->find($value);
        if (!in_array($user->role->name, $allowedRoles)) {
            $fail('Invalid role. Please choose a teacher');
        }
    }
}