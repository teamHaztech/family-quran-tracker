<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;

/**
 * Reads the bundled Quran dataset (Arabic Uthmani + Saheeh International
 * translation) from resources/quran. No database or external API needed,
 * so it works offline and on shared hosting.
 */
class QuranService
{
    protected string $base;

    public function __construct()
    {
        $this->base = base_path('resources/quran');
    }

    /**
     * The list of all 114 surahs with metadata.
     */
    public function surahs(): array
    {
        return Cache::rememberForever('quran.index', function () {
            $path = "{$this->base}/index.json";

            return is_file($path) ? json_decode(file_get_contents($path), true) : [];
        });
    }

    /**
     * A single surah with its ayahs (arabic + translation), or null.
     */
    public function surah(int $number): ?array
    {
        if ($number < 1 || $number > 114) {
            return null;
        }

        return Cache::rememberForever("quran.surah.{$number}", function () use ($number) {
            $path = "{$this->base}/surahs/{$number}.json";

            return is_file($path) ? json_decode(file_get_contents($path), true) : null;
        });
    }

    /**
     * Lightweight meta for a surah (from the index) — used for prev/next.
     */
    public function meta(int $number): ?array
    {
        foreach ($this->surahs() as $s) {
            if ($s['number'] === $number) {
                return $s;
            }
        }

        return null;
    }
}
