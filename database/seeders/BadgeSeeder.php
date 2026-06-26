<?php

namespace Database\Seeders;

use App\Models\Badge;
use Illuminate\Database\Seeder;

class BadgeSeeder extends Seeder
{
    public function run(): void
    {
        $badges = [
            ['key' => 'first_reading', 'name' => 'First Reading', 'description' => 'Logged your very first reading session', 'icon' => '🌱', 'threshold_type' => 'first', 'threshold_value' => 1],
            ['key' => 'streak_7', 'name' => '7 Day Streak', 'description' => 'Read every day for 7 days in a row', 'icon' => '🔥', 'threshold_type' => 'streak', 'threshold_value' => 7],
            ['key' => 'streak_30', 'name' => '30 Day Streak', 'description' => 'Read every day for 30 days in a row', 'icon' => '⚡', 'threshold_type' => 'streak', 'threshold_value' => 30],
            ['key' => 'pages_100', 'name' => '100 Pages', 'description' => 'Read a total of 100 pages', 'icon' => '📗', 'threshold_type' => 'pages', 'threshold_value' => 100],
            ['key' => 'pages_500', 'name' => '500 Pages', 'description' => 'Read a total of 500 pages', 'icon' => '📘', 'threshold_type' => 'pages', 'threshold_value' => 500],
            ['key' => 'pages_1000', 'name' => '1000 Pages', 'description' => 'Read a total of 1000 pages', 'icon' => '🏆', 'threshold_type' => 'pages', 'threshold_value' => 1000],
        ];

        foreach ($badges as $badge) {
            Badge::updateOrCreate(['key' => $badge['key']], $badge);
        }
    }
}
