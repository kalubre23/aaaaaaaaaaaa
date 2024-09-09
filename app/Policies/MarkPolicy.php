<?php

namespace App\Policies;

use App\Models\Mark;
use App\Models\User;

class MarkPolicy
{
    public function before(User $user, string $ability): bool|null
    {
        return $user->isAdmin() ? true : null;
    }

    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->isTeacher() || $user->isParent() || $user->isStudent();
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Mark $record): bool
    {
        return $user->isTeacher() || $user->isParent() || $user->isStudent();
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->isTeacher();
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Mark $record): bool
    {
        return $user->isTeacher() && $user->id===$record->inserted_by;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Mark $record): bool
    {
        return $user->isTeacher() && $user->id===$record->inserted_by;
    }
}
