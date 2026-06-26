<?php

namespace App\Services;

use App\Models\Badge;
use App\Models\User;
use Illuminate\Support\Collection;

class BadgeService
{
    public function __construct(protected StreakService $streaks)
    {
    }

    /**
     * Evaluate all badge criteria for a user and award any newly-earned badges.
     *
     * @return Collection<int, Badge> Newly awarded badges (for congrats popup).
     */
    public function evaluate(User $user): Collection
    {
        $totalPages = (int) $user->readingSessions()->sum('pages_read');
        $totalSessions = (int) $user->readingSessions()->count();
        $longestStreak = $this->streaks->longestStreak($user);

        $earnedKeys = $user->badges()->pluck('badges.key')->all();
        $newlyAwarded = collect();

        foreach (Badge::all() as $badge) {
            if (in_array($badge->key, $earnedKeys, true)) {
                continue;
            }

            $qualifies = match ($badge->threshold_type) {
                'first' => $totalSessions >= 1,
                'pages' => $totalPages >= $badge->threshold_value,
                'sessions' => $totalSessions >= $badge->threshold_value,
                'streak' => $longestStreak >= $badge->threshold_value,
                default => false,
            };

            if ($qualifies) {
                $user->badges()->attach($badge->id, ['awarded_at' => now()]);
                $newlyAwarded->push($badge);
            }
        }

        return $newlyAwarded;
    }
}
