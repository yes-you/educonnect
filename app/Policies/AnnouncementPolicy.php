<?php

namespace App\Policies;

use App\Models\Announcement;
use App\Models\User;

class AnnouncementPolicy
{
    // Admin passes every check.
    public function before(User $user, string $ability): ?bool
    {
        return $user->hasRole('admin') ? true : null;
    }

    public function delete(User $user, Announcement $announcement): bool
    {
        return $user->id === $announcement->user_id;
    }
}
