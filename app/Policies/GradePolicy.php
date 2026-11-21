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
