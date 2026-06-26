<?php

namespace App\Http\Controllers;

use App\Models\Setting;
use App\Services\StatsService;
use Illuminate\Http\Request;
use Illuminate\View\View;

class LeaderboardController extends Controller
{
    public function index(Request $request, StatsService $stats): View
    {
        abort_unless(Setting::current()->enable_leaderboard, 404);

        $period = $request->get('period', 'all'); // weekly | monthly | all

        [$from, $to] = match ($period) {
            'weekly' => [now()->startOfWeek(), now()->endOfWeek()],
            'monthly' => [now()->startOfMonth(), now()->endOfMonth()],
            default => [null, null],
        };

        $rankings = $stats->rankedMembers('desc', $from, $to)
            ->filter(fn ($m) => $m->total_pages > 0)
            ->values();

        return view('leaderboard.index', compact('rankings', 'period'));
    }
}
