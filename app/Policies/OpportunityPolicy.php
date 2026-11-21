<?php

namespace App\Policies;

use App\Models\Opportunity;
use App\Models\User;

class OpportunityPolicy
{
    // Admin passes every check.
    public function before(User $user, string $ability): ?bool
    {
        return $user->hasRole('admin') ? true : null;
    }

    // Only the organization that posted it may edit or delete it.
    public function update(User $user, Opportunity $opportunity): bool
    {
        return $user->id === $opportunity->user_id;
    }

    public function delete(User $user, Opportunity $opportunity): bool
    {
        return $user->id === $opportunity->user_id;
    }
}
