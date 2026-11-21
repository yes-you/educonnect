<?php

namespace App\Policies;

use App\Models\Module;
use App\Models\User;

class ModulePolicy
{
    // Admin passes every check.
    public function before(User $user, string $ability): ?bool
    {
        return $user->hasRole('admin') ? true : null;
    }

    public function update(User $user, Module $module): bool
    {
        return $user->id === $module->teacher_id;
    }

    public function delete(User $user, Module $module): bool
    {
        return $user->id === $module->teacher_id;
    }
}
