<?php

namespace App\Policies;

use App\Models\ForumThread;
use App\Models\User;

class ForumThreadPolicy
{
    // Admin passes every check.
    public function before(User $user, string $ability): ?bool
    {
        return $user->hasRole('admin') ? true : null;
    }

    public function delete(User $user, ForumThread $thread): bool
    {
        return $user->id === $thread->user_id;
    }
}
