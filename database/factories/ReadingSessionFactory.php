<?php

namespace Database\Factories;

use App\Models\ReadingSession;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<ReadingSession>
 */
class ReadingSessionFactory extends Factory
{
    protected $model = ReadingSession::class;

    /** A handful of well-known surah names for realistic demo data. */
    protected array $surahs = [
        'Al-Fatihah', 'Al-Baqarah', 'Aal-i-Imran', 'An-Nisa', 'Al-Maidah',
        'Al-Anam', 'Al-Araf', 'Al-Anfal', 'At-Tawbah', 'Yunus',
        'Hud', 'Yusuf', 'Ar-Rad', 'Ibrahim', 'Al-Hijr', 'An-Nahl',
        'Al-Isra', 'Al-Kahf', 'Maryam', 'Ta-Ha', 'Ya-Sin', 'Al-Mulk',
    ];

    public function definition(): array
    {
        $startPage = fake()->numberBetween(1, 600);
        $pages = fake()->numberBetween(1, 8);
        $endPage = min(604, $startPage + $pages);
        $pagesRead = $endPage - $startPage + 1;
        $minutes = $pagesRead * fake()->numberBetween(2, 5);

        return [
            'user_id' => User::factory(),
            'date' => fake()->dateTimeBetween('-60 days', 'now')->format('Y-m-d'),
            'surah' => fake()->randomElement($this->surahs),
            'start_page' => $startPage,
            'end_page' => $endPage,
            'pages_read' => $pagesRead,
            'minutes_read' => $minutes,
            'juz' => fake()->numberBetween(1, 30),
            'notes' => fake()->optional(0.3)->sentence(),
            'method' => fake()->randomElement(['manual', 'timer']),
        ];
    }
}
