<?php

namespace Database\Seeders;

use App\Models\Setting;
use Illuminate\Database\Seeder;

class SettingSeeder extends Seeder
{
    public function run(): void
    {
        Setting::updateOrCreate(['id' => 1], [
            'family_name' => 'The Blessed Family',
            'theme' => 'light',
            'daily_goal_pages' => 5,
            'monthly_goal_pages' => 150,
            'enable_leaderboard' => true,
            'enable_badges' => true,
        ]);
    }
}
