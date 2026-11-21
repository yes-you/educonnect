<?php

namespace App\Policies;

use App\Models\StudyGroup;
use App\Models\User;

class StudyGroupPolicy
{
    // Admin passes every check.
    public function before(User $user, string $ability): ?bool
    {
        return $user->hasRole('admin') ? true : null;
    }

    // Only members may open a group's page.
    public function view(User $user, StudyGroup $group): bool
    {
        return $group->hasMember($user);
    }

    // Only members may post messages.
    public function post(User $user, StudyGroup $group): bool
    {
        return $group->hasMember($user);
    }

    // Only the owner may delete the group.
    public function delete(User $user, StudyGroup $group): bool
    {
        return $user->id === $group->owner_id;
    }
}
