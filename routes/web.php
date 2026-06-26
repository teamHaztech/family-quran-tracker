<?php

use App\Http\Controllers\Admin\ActivityLogController;
use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Admin\MemberController;
use App\Http\Controllers\Admin\ReportController;
use App\Http\Controllers\Admin\SettingController;
use App\Http\Controllers\BadgeController;
use App\Http\Controllers\InstallController;
use App\Http\Controllers\LeaderboardController;
use App\Http\Controllers\Member\DashboardController as MemberDashboardController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\QuranController;
use App\Http\Controllers\ReadingSessionController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Public entry
|--------------------------------------------------------------------------
*/
Route::get('/', function () {
    return Auth::check() ? redirect()->route('dashboard') : redirect()->route('login');
});

/*
|--------------------------------------------------------------------------
| One-time web installer (no-SSH hosts like Hostinger)
|--------------------------------------------------------------------------
| Visit /install/<DEPLOY_KEY> once after deploying to migrate the database,
| seed badges/settings and create the first admin. 404s when DEPLOY_KEY is
| blank — clear it in .env afterwards to disable.
*/
// Session/cookie/CSRF middleware are stripped so the installer can run against
// an empty database (database session/cache tables don't exist yet).
Route::get('/install/{token}', [InstallController::class, 'run'])
    ->withoutMiddleware([
        \Illuminate\Cookie\Middleware\EncryptCookies::class,
        \Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse::class,
        \Illuminate\Session\Middleware\StartSession::class,
        \Illuminate\View\Middleware\ShareErrorsFromSession::class,
        \Illuminate\Foundation\Http\Middleware\ValidateCsrfToken::class,
    ]);

/*
|--------------------------------------------------------------------------
| Role-based dashboard dispatcher
|--------------------------------------------------------------------------
| A single "dashboard" route keeps Breeze links working while sending each
| user to the correct dashboard for their role.
*/
Route::get('/dashboard', function () {
    return Auth::user()->isAdmin()
        ? redirect()->route('admin.dashboard')
        : redirect()->route('member.dashboard');
})->middleware(['auth', 'active'])->name('dashboard');

/*
|--------------------------------------------------------------------------
| Authenticated (any active user)
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'active'])->group(function () {
    // Quran reader (all 114 surahs with translation) — available to everyone
    Route::get('/quran', [QuranController::class, 'index'])->name('quran.index');
    Route::get('/quran/{surah}', [QuranController::class, 'show'])->whereNumber('surah')->name('quran.show');

    // Profile (Breeze) + avatar upload
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::patch('/profile/avatar', [ProfileController::class, 'updateAvatar'])->name('profile.avatar');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

/*
|--------------------------------------------------------------------------
| Member area
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'active'])->group(function () {
    Route::get('/member/dashboard', [MemberDashboardController::class, 'index'])->name('member.dashboard');

    // Reading sessions (manual entry + timer + history)
    Route::get('/reading', [ReadingSessionController::class, 'index'])->name('reading.index');
    Route::get('/reading/create', [ReadingSessionController::class, 'create'])->name('reading.create');
    Route::get('/reading/timer', [ReadingSessionController::class, 'timer'])->name('reading.timer');
    Route::post('/reading', [ReadingSessionController::class, 'store'])->name('reading.store');
    Route::get('/reading/{reading}/edit', [ReadingSessionController::class, 'edit'])->name('reading.edit');
    Route::put('/reading/{reading}', [ReadingSessionController::class, 'update'])->name('reading.update');
    Route::delete('/reading/{reading}', [ReadingSessionController::class, 'destroy'])->name('reading.destroy');

    // Gamification
    Route::get('/leaderboard', [LeaderboardController::class, 'index'])->name('leaderboard.index');
    Route::get('/badges', [BadgeController::class, 'index'])->name('badges.index');
});

/*
|--------------------------------------------------------------------------
| Admin area (Family Leader only)
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'active', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('dashboard');

    // Member management
    Route::resource('members', MemberController::class);
    Route::patch('members/{member}/disable', [MemberController::class, 'disable'])->name('members.disable');
    Route::patch('members/{member}/enable', [MemberController::class, 'enable'])->name('members.enable');
    Route::patch('members/{member}/reset-password', [MemberController::class, 'resetPassword'])->name('members.reset-password');

    // Reports + exports
    Route::get('/reports', [ReportController::class, 'index'])->name('reports.index');
    Route::get('/reports/export', [ReportController::class, 'export'])->name('reports.export');

    // Settings
    Route::get('/settings', [SettingController::class, 'edit'])->name('settings.edit');
    Route::put('/settings', [SettingController::class, 'update'])->name('settings.update');

    // Activity log
    Route::get('/activity', [ActivityLogController::class, 'index'])->name('activity.index');
});

require __DIR__.'/auth.php';
