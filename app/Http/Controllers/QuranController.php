<?php

namespace App\Http\Controllers;

use App\Services\QuranService;
use Illuminate\Http\Request;
use Illuminate\View\View;

class QuranController extends Controller
{
    public function __construct(protected QuranService $quran)
    {
    }

    /** List of all 114 surahs (searchable). */
    public function index(Request $request): View
    {
        $surahs = collect($this->quran->surahs());

        if ($search = trim((string) $request->get('search'))) {
            $surahs = $surahs->filter(function ($s) use ($search) {
                return stripos($s['englishName'], $search) !== false
                    || stripos($s['englishTranslation'], $search) !== false
                    || str_contains((string) $s['number'], $search)
                    || str_contains($s['name'], $search);
            })->values();
        }

        return view('quran.index', ['surahs' => $surahs]);
    }

    /** Read a single surah (Arabic + translation). */
    public function show(int $surah): View
    {
        $data = $this->quran->surah($surah);

        abort_if($data === null, 404);

        return view('quran.show', [
            'surah' => $data,
            'prev' => $surah > 1 ? $this->quran->meta($surah - 1) : null,
            'next' => $surah < 114 ? $this->quran->meta($surah + 1) : null,
        ]);
    }
}
