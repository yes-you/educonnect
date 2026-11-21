<?php

namespace App\Policies;

use App\Models\Quiz;
use App\Models\User;

class QuizPolicy
{
    // Admin passes every check.
    public function before(User $user, string $ability): ?bool
    {
        return $user->hasRole('admin') ? true : null;
    }

    public function update(User $user, Quiz $quiz): bool
    {
        return $user->id === $quiz->teacher_id;
    }

    public function delete(User $user, Quiz $quiz): bool
    {
        return $user->id === $quiz->teacher_id;
    }

    // Open the manage page, add or remove questions, publish.
    public function manage(User $user, Quiz $quiz): bool
    {
        return $user->id === $quiz->teacher_id;
    }
}
