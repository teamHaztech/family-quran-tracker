<?php

namespace App\Services;

use App\Models\ReadingSession;
use App\Models\Setting;
use App\Models\User;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class StatsService
{
    public function __construct(protected StreakService $streaks)
    {
    }

    /*
    |--------------------------------------------------------------------------
    | Member (personal) statistics
    |--------------------------------------------------------------------------
    */
    public function memberStats(User $user): array
    {
        $sessions = $user->readingSessions();
        $settings = Setting::current();
        $dailyGoal = $user->profile?->daily_goal_pages ?: $settings->daily_goal_pages;
        $monthlyGoal = $settings->monthly_goal_pages;

        $todayPages = (int) (clone $sessions)->whereDate('date', today())->sum('pages_read');
        $todayMinutes = (int) (clone $sessions)->whereDate('date', today())->sum('minutes_read');
        $weekPages = (int) (clone $sessions)->whereBetween('date', [now()->startOfWeek(), now()->endOfWeek()])->sum('pages_read');
        $monthPages = (int) (clone $sessions)->whereBetween('date', [now()->startOfMonth(), now()->endOfMonth()])->sum('pages_read');
        $monthMinutes = (int) (clone $sessions)->whereBetween('date', [now()->startOfMonth(), now()->endOfMonth()])->sum('minutes_read');

        return [
            'today_pages' => $todayPages,
            'today_minutes' => $todayMinutes,
            'week_pages' => $weekPages,
            'month_pages' => $monthPages,
            'month_minutes' => $monthMinutes,
            'total_pages' => (int) (clone $sessions)->sum('pages_read'),
            'total_minutes' => (int) (clone $sessions)->sum('minutes_read'),
            'total_sessions' => (int) (clone $sessions)->count(),
            'current_streak' => $this->streaks->currentStreak($user),
            'longest_streak' => $this->streaks->longestStreak($user),
            'daily_goal' => $dailyGoal,
            'monthly_goal' => $monthlyGoal,
            'daily_goal_pct' => $dailyGoal ? min(100, (int) round($todayPages / $dailyGoal * 100)) : 0,
            'monthly_goal_pct' => $monthlyGoal ? min(100, (int) round($monthPages / $monthlyGoal * 100)) : 0,
            'logged_today' => $todayPages > 0 || (clone $sessions)->whereDate('date', today())->exists(),
            'badge_count' => $user->badges()->count(),
        ];
    }

    /*
    |--------------------------------------------------------------------------
    | Admin (family-wide) statistics
    |--------------------------------------------------------------------------
    */
    public function adminStats(): array
    {
        $base = ReadingSession::query();

        $mostActive = $this->rankedMembers('desc')->first();
        $leastActive = $this->rankedMembers('asc')->first();

        // Family streak = the best current streak among all members.
        $familyStreak = User::all()->map(fn ($u) => $this->streaks->currentStreak($u))->max() ?? 0;

        return [
            'total_members' => User::members()->count(),
            'active_members' => User::members()->active()->count(),
            'today_pages' => (int) (clone $base)->whereDate('date', today())->sum('pages_read'),
            'today_minutes' => (int) (clone $base)->whereDate('date', today())->sum('minutes_read'),
            'week_pages' => (int) (clone $base)->whereBetween('date', [now()->startOfWeek(), now()->endOfWeek()])->sum('pages_read'),
            'month_pages' => (int) (clone $base)->whereBetween('date', [now()->startOfMonth(), now()->endOfMonth()])->sum('pages_read'),
            'month_minutes' => (int) (clone $base)->whereBetween('date', [now()->startOfMonth(), now()->endOfMonth()])->sum('minutes_read'),
            'total_pages' => (int) (clone $base)->sum('pages_read'),
            'total_minutes' => (int) (clone $base)->sum('minutes_read'),
            'total_sessions' => (int) (clone $base)->count(),
            'most_active' => $mostActive,
            'least_active' => $leastActive,
            'family_streak' => $familyStreak,
            'badges_awarded' => DB::table('user_badges')->count(),
        ];
    }

    /**
     * Members ranked by total pages read (returns objects with name + total).
     */
    public function rankedMembers(string $direction = 'desc', ?Carbon $from = null, ?Carbon $to = null)
    {
        $query = User::members()
            ->leftJoin('reading_sessions', 'reading_sessions.user_id', '=', 'users.id')
            ->when($from && $to, fn ($q) => $q->whereBetween('reading_sessions.date', [$from, $to]))
            ->groupBy('users.id', 'users.name', 'users.photo')
            ->select('users.id', 'users.name', 'users.photo', DB::raw('COALESCE(SUM(reading_sessions.pages_read),0) as total_pages'), DB::raw('COALESCE(SUM(reading_sessions.minutes_read),0) as total_minutes'))
            ->orderBy('total_pages', $direction);

        return $query->get();
    }

    /*
    |--------------------------------------------------------------------------
    | Chart datasets
    |--------------------------------------------------------------------------
    */

    /**
     * Daily pages read over the last N days. Optionally scoped to one user.
     */
    public function dailyPages(int $days = 30, ?User $user = null): array
    {
        $from = today()->subDays($days - 1);

        $rows = ReadingSession::query()
            ->when($user, fn ($q) => $q->where('user_id', $user->id))
            ->whereDate('date', '>=', $from)
            ->groupBy('d')
            ->select(DB::raw('DATE(date) as d'), DB::raw('SUM(pages_read) as pages'))
            ->pluck('pages', 'd');

        return $this->fillDailySeries($from, today(), $rows);
    }

    /**
     * Weekly total pages over the last N weeks.
     */
    public function weeklyActivity(int $weeks = 8, ?User $user = null): array
    {
        $labels = [];
        $data = [];
        for ($i = $weeks - 1; $i >= 0; $i--) {
            $start = now()->startOfWeek()->subWeeks($i);
            $end = (clone $start)->endOfWeek();
            $labels[] = $start->format('M d');
            $data[] = (int) ReadingSession::query()
                ->when($user, fn ($q) => $q->where('user_id', $user->id))
                ->whereBetween('date', [$start, $end])
                ->sum('pages_read');
        }

        return ['labels' => $labels, 'data' => $data];
    }

    /**
     * Monthly reading time (minutes) over the last N months.
     */
    public function monthlyTime(int $months = 12, ?User $user = null): array
    {
        $labels = [];
        $data = [];
        for ($i = $months - 1; $i >= 0; $i--) {
            $start = now()->startOfMonth()->subMonths($i);
            $end = (clone $start)->endOfMonth();
            $labels[] = $start->format('M Y');
            $data[] = (int) ReadingSession::query()
                ->when($user, fn ($q) => $q->where('user_id', $user->id))
                ->whereBetween('date', [$start, $end])
                ->sum('minutes_read');
        }

        return ['labels' => $labels, 'data' => $data];
    }

    /**
     * Top readers chart dataset (by total pages).
     */
    public function topReaders(int $limit = 5, ?Carbon $from = null, ?Carbon $to = null): array
    {
        $members = $this->rankedMembers('desc', $from, $to)->take($limit);

        return [
            'labels' => $members->pluck('name')->all(),
            'data' => $members->pluck('total_pages')->map(fn ($v) => (int) $v)->all(),
        ];
    }

    /**
     * Calendar heatmap data: date => pages, for the last N days.
     */
    public function heatmap(int $days = 120, ?User $user = null): array
    {
        $from = today()->subDays($days - 1);

        $rows = ReadingSession::query()
            ->when($user, fn ($q) => $q->where('user_id', $user->id))
            ->whereDate('date', '>=', $from)
            ->groupBy('d')
            ->select(DB::raw('DATE(date) as d'), DB::raw('SUM(pages_read) as pages'))
            ->pluck('pages', 'd');

        $out = [];
        foreach ($rows as $d => $pages) {
            $out[Carbon::parse($d)->toDateString()] = (int) $pages;
        }

        return $out;
    }

    /**
     * Fill a continuous daily series with zeros for missing days.
     */
    protected function fillDailySeries(Carbon $from, Carbon $to, $rows): array
    {
        $labels = [];
        $data = [];
        $cursor = $from->copy();

        $map = [];
        foreach ($rows as $d => $pages) {
            $map[Carbon::parse($d)->toDateString()] = (int) $pages;
        }

        while ($cursor->lte($to)) {
            $key = $cursor->toDateString();
            $labels[] = $cursor->format('M d');
            $data[] = $map[$key] ?? 0;
            $cursor->addDay();
        }

        return ['labels' => $labels, 'data' => $data];
    }
}
