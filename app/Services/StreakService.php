<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Carbon;

class StreakService
{
    /**
     * Return distinct reading dates (Y-m-d strings) for a user, descending.
     *
     * @return array<int, string>
     */
    protected function readingDates(User $user): array
    {
        return $user->readingSessions()
            ->selectRaw('DATE(date) as d')
            ->distinct()
            ->orderByDesc('d')
            ->pluck('d')
            ->map(fn ($d) => Carbon::parse($d)->toDateString())
            ->all();
    }

    /**
     * Current consecutive-day streak ending today or yesterday.
     * Reading yesterday but not yet today keeps the streak alive.
     */
    public function currentStreak(User $user): int
    {
        $dates = $this->readingDates($user);

        if (empty($dates)) {
            return 0;
        }

        $today = Carbon::today();
        $latest = Carbon::parse($dates[0]);

        // Streak is broken if the last reading was before yesterday.
        if ($latest->lt($today->copy()->subDay())) {
            return 0;
        }

        $streak = 1;
        for ($i = 0; $i < count($dates) - 1; $i++) {
            $curr = Carbon::parse($dates[$i]);
            $prev = Carbon::parse($dates[$i + 1]);

            if ((int) abs($prev->diffInDays($curr)) === 1) {
                $streak++;
            } else {
                break;
            }
        }

        return $streak;
    }

    /**
     * Longest consecutive-day streak ever achieved.
     */
    public function longestStreak(User $user): int
    {
        $dates = array_reverse($this->readingDates($user)); // ascending

        if (empty($dates)) {
            return 0;
        }

        $longest = 1;
        $run = 1;

        for ($i = 1; $i < count($dates); $i++) {
            $prev = Carbon::parse($dates[$i - 1]);
            $curr = Carbon::parse($dates[$i]);
            $diff = (int) abs($prev->diffInDays($curr));

            if ($diff === 1) {
                $run++;
                $longest = max($longest, $run);
            } elseif ($diff === 0) {
                // same day, ignore
                continue;
            } else {
                $run = 1;
            }
        }

        return $longest;
    }
}
