<?php

namespace App\Policies;

use App\Models\ForumReply;
use App\Models\User;

class ForumReplyPolicy
{
    // Admin passes every check.
    public function before(User $user, string $ability): ?bool
    {
        return $user->hasRole('admin') ? true : null;
    }

    public function delete(User $user, ForumReply $reply): bool
    {
        return $user->id === $reply->user_id;
    }
}
