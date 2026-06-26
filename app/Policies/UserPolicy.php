<?php

namespace App\Policies;

use App\Models\User;

class UserPolicy
{
    /** Only the family leader manages member accounts. */
    public function manage(User $user): bool
    {
        return $user->isAdmin();
    }

    public function update(User $user, User $member): bool
    {
        return $user->isAdmin();
    }

    public function delete(User $user, User $member): bool
    {
        // Admins may delete members, but never themselves.
        return $user->isAdmin() && $user->id !== $member->id;
    }
}
