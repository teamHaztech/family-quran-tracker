<?php

namespace App\Http\Controllers\Member;

use App\Http\Controllers\Controller;
use App\Services\StatsService;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(StatsService $stats): View
    {
        $user = auth()->user();

        $data = $stats->memberStats($user);
        $dailyPages = $stats->dailyPages(30, $user);
        $weeklyActivity = $stats->weeklyActivity(8, $user);
        $heatmap = $stats->heatmap(119, $user);

        $recent = $user->readingSessions()->latest('date')->latest('id')->limit(6)->get();
        $badges = $user->badges()->latest('user_badges.created_at')->limit(6)->get();

        return view('member.dashboard', compact('data', 'dailyPages', 'weeklyActivity', 'heatmap', 'recent', 'badges'));
    }
}
