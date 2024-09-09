<?php

namespace App\Policies;

use App\Models\Subject;
use App\Models\User;

class SubjectPolicy
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
        return $user->isTeacher();
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Subject $record): bool
    {
        return $user->isTeacher() && $user->id === $record->teacher_id;
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
    public function update(User $user, Subject $record): bool
    {
        return $user->isTeacher() && $user->id === $record->teacher_id;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Subject $record): bool
    {
        return $user->isTeacher() && $user->id === $record->teacher_id;
    }

    public function add_students(User $user, Subject $record): bool
    {
        return $user->isTeacher();
    }

    public function remove_students(User $user, Subject $record): bool
    {
        return $user->isTeacher();
    }
}
