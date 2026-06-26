<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class Setting extends Model
{
    protected $fillable = [
        'family_name',
        'family_logo',
        'theme',
        'daily_goal_pages',
        'monthly_goal_pages',
        'enable_leaderboard',
        'enable_badges',
    ];

    protected function casts(): array
    {
        return [
            'enable_leaderboard' => 'boolean',
            'enable_badges' => 'boolean',
        ];
    }

    /**
     * Retrieve the single settings row (cached), creating defaults if missing.
     */
    public static function current(): self
    {
        return Cache::rememberForever('app_settings', function () {
            return static::firstOrCreate([], [
                'family_name' => 'My Family',
                'theme' => 'light',
                'daily_goal_pages' => config('quran.default_daily_goal_pages'),
                'monthly_goal_pages' => config('quran.default_monthly_goal_pages'),
                'enable_leaderboard' => true,
                'enable_badges' => true,
            ]);
        });
    }

    protected static function booted(): void
    {
        static::saved(fn () => Cache::forget('app_settings'));
        static::deleted(fn () => Cache::forget('app_settings'));
    }
}
