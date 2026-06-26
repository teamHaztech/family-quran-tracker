<?php

namespace Database\Seeders;

use App\Models\Profile;
use App\Models\ReadingSession;
use App\Models\User;
use App\Services\BadgeService;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            BadgeSeeder::class,
            SettingSeeder::class,
        ]);

        // --- Family Leader (Admin) ---
        $admin = User::factory()->admin()->create([
            'first_name' => 'Ahmad',
            'last_name' => 'Khan',
            'name' => 'Ahmad Khan',
            'email' => 'admin@example.com',
            'date_joined' => now()->subYear(),
        ]);
        Profile::create(['user_id' => $admin->id, 'theme' => 'light']);
        $this->seedSessionsFor($admin, streakDays: 12, scatter: 30);

        // --- Family Members ---
        $members = [
            ['Fatima', 'Khan', 'fatima@example.com', 15, 40],
            ['Yusuf', 'Khan', 'yusuf@example.com', 7, 25],
            ['Maryam', 'Khan', 'maryam@example.com', 30, 50],
            ['Ibrahim', 'Khan', 'ibrahim@example.com', 3, 18],
            ['Aisha', 'Khan', 'aisha@example.com', 0, 5],
        ];

        foreach ($members as [$first, $last, $email, $streak, $scatter]) {
            $member = User::factory()->create([
                'first_name' => $first,
                'last_name' => $last,
                'name' => "$first $last",
                'email' => $email,
                'date_joined' => now()->subMonths(rand(1, 10)),
            ]);
            Profile::create(['user_id' => $member->id, 'theme' => 'light']);
            $this->seedSessionsFor($member, streakDays: $streak, scatter: $scatter);
        }

        // --- A disabled member to demonstrate the status feature ---
        $disabled = User::factory()->disabled()->create([
            'first_name' => 'Zayd',
            'last_name' => 'Khan',
            'name' => 'Zayd Khan',
            'email' => 'zayd@example.com',
        ]);
        Profile::create(['user_id' => $disabled->id]);

        // --- Award badges based on the seeded history ---
        $badges = app(BadgeService::class);
        foreach (User::all() as $user) {
            $badges->evaluate($user);
        }
    }

    /**
     * Create demo reading sessions: a recent consecutive streak plus older
     * scattered sessions, so dashboards, charts and streaks all have data.
     */
    protected function seedSessionsFor(User $user, int $streakDays, int $scatter): void
    {
        $dates = collect();

        // Recent consecutive streak ending today.
        for ($i = 0; $i < $streakDays; $i++) {
            $dates->push(Carbon::today()->subDays($i)->toDateString());
        }

        // Older scattered reading days within the last 90 days.
        for ($i = 0; $i < $scatter; $i++) {
            $dates->push(Carbon::today()->subDays(rand($streakDays + 1, 90))->toDateString());
        }

        foreach ($dates->unique() as $date) {
            ReadingSession::factory()
                ->for($user)
                ->create(['date' => $date]);
        }
    }
}
