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

    // Who may open a student's report card: the student, a parent linked to them, and a teacher who encoded
    // at least one of that student's grades (the same limit as the grades list).
    // (Admin passes in before().) Called as Gate::authorize('viewReport', [Grade::class, $student]).
    public function viewReport(User $user, User $student): bool
    {
        if ($user->hasRole('teacher')) {
            return Grade::where('teacher_id', $user->id)->where('student_id', $student->id)->exists();
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
