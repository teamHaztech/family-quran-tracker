<?php

namespace App\Policies;

use App\Models\ReadingSession;
use App\Models\User;

class ReadingSessionPolicy
{
    /** Admins may act on any session; members only on their own. */
    public function view(User $user, ReadingSession $session): bool
    {
        return $user->isAdmin() || $user->id === $session->user_id;
    }

    public function update(User $user, ReadingSession $session): bool
    {
        return $user->id === $session->user_id;
    }

    public function delete(User $user, ReadingSession $session): bool
    {
        return $user->isAdmin() || $user->id === $session->user_id;
    }
}
