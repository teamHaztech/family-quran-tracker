<?php

namespace App\Http\Controllers;

use App\Models\Badge;
use App\Models\Setting;
use Illuminate\View\View;

class BadgeController extends Controller
{
    public function index(): View
    {
        abort_unless(Setting::current()->enable_badges, 404);

        $user = auth()->user();
        $allBadges = Badge::orderBy('threshold_value')->get();
        $earnedIds = $user->badges()->pluck('badges.id')->all();

        return view('badges.index', compact('allBadges', 'earnedIds'));
    }
}
