<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\StatsService;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(StatsService $stats): View
    {
        $data = $stats->adminStats();
        $dailyPages = $stats->dailyPages(30);
        $weeklyActivity = $stats->weeklyActivity(8);
        $monthlyTime = $stats->monthlyTime(12);
        $topReaders = $stats->topReaders(5);
        $heatmap = $stats->heatmap(119);

        return view('admin.dashboard', compact('data', 'dailyPages', 'weeklyActivity', 'monthlyTime', 'topReaders', 'heatmap'));
    }
}
