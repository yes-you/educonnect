<?php

namespace App\Policies;

use App\Models\Grade;
use App\Models\User;

class GradePolicy
{
    // Admin passes every check.
    public function before(User $user, string $ability): ?bool
    {
        return $user->hasRole('admin') ? true : null;
    }

    // Who may open a student's report card: the student, a parent linked to them, and teachers.
    // (Admin passes in before().) Called as Gate::authorize('viewReport', [Grade::class, $student]).
    public function viewReport(User $user, User $student): bool
    {
        if ($user->hasRole('teacher')) {
            return true;
        }

        if ($user->hasRole('student')) {
            return $user->id === $student->id;
        }

        return $user->hasRole('parent')
            && $user->children()->where('users.id', $student->id)->exists();
    }

    // Only the teacher who encoded a grade may change or remove it.
    public function update(User $user, Grade $grade): bool
    {
        return $user->id === $grade->teacher_id;
    }

    public function delete(User $user, Grade $grade): bool
    {
        return $user->id === $grade->teacher_id;
    }
}
