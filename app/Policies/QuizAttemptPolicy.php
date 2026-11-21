<?php

namespace App\Policies;

use App\Models\QuizAttempt;
use App\Models\User;

class QuizAttemptPolicy
{
    // Admin passes every check.
    public function before(User $user, string $ability): ?bool
    {
        return $user->hasRole('admin') ? true : null;
    }

    // The student who took it, the quiz's teacher, or a parent linked to that student.
    public function view(User $user, QuizAttempt $attempt): bool
    {
        if ($user->id === $attempt->user_id) {
            return true;
        }

        if ($user->id === $attempt->quiz->teacher_id) {
            return true;
        }

        return $user->hasRole('parent')
            && $user->children()->where('users.id', $attempt->user_id)->exists();
    }
}
