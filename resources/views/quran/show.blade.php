<x-app-layout>
    <x-slot name="title">{{ $surah['englishName'] }}</x-slot>

    <div class="max-w-3xl mx-auto space-y-5" x-data="{ showTranslation: true, fontScale: 1 }">

        <a href="{{ route('quran.index') }}" class="text-sm text-slate-500 hover:text-brand-600 inline-flex items-center gap-1">← All Surahs</a>

        {{-- Surah header --}}
        <div class="card p-6 text-center bg-gradient-to-br from-brand-700 to-brand-900 text-white relative overflow-hidden">
            <div class="absolute -top-14 -right-10 w-48 h-48 rounded-full bg-white/5"></div>
            <div class="relative">
                <p class="text-brand-100 text-xs uppercase tracking-widest">Surah {{ $surah['number'] }}</p>
                <h2 class="font-quran text-4xl mt-1" dir="rtl">{{ \Illuminate\Support\Str::of($surah['name'])->replace('سُورَةُ ', '') }}</h2>
                <p class="text-xl font-extrabold mt-2">{{ $surah['englishName'] }}</p>
                <p class="text-brand-100 text-sm">{{ $surah['englishTranslation'] }} · {{ $surah['numberOfAyahs'] }} Ayahs · {{ $surah['revelationType'] }}</p>
            </div>
        </div>

        {{-- Reading controls --}}
        <div class="card p-3 flex flex-wrap items-center gap-2 sticky top-[4.5rem] z-10" x-data="readingTimer">
            {{-- Timer --}}
            <div class="flex items-center gap-2">
                <button x-show="!running" @click="start()" class="btn-primary !py-2 !px-3 text-xs">
                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M8 5v14l11-7z"/></svg>
                    <span x-text="seconds > 0 ? 'Resume' : 'Start Reading'"></span>
                </button>
                <button x-show="running" x-cloak @click="stop()" class="btn-danger !py-2 !px-3 text-xs">
                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><rect x="6" y="6" width="12" height="12" rx="2"/></svg>
                    Stop &amp; Log
                </button>
                <span x-show="seconds > 0" x-cloak class="font-mono text-sm font-bold text-brand-600 tabular-nums" x-text="display"></span>
            </div>

            <div class="flex items-center gap-2 ml-auto">
                {{-- Translation toggle --}}
                <button @click="showTranslation = !showTranslation"
                        class="btn-secondary !py-2 !px-3 text-xs"
                        :class="showTranslation ? '!bg-brand-100 !text-brand-700 dark:!bg-brand-900/40 dark:!text-brand-300' : ''">
                    Translation
                </button>
                {{-- Font size --}}
                <div class="flex items-center rounded-xl bg-slate-100 dark:bg-slate-700">
                    <button @click="fontScale = Math.max(0.8, fontScale - 0.1)" class="w-8 h-8 grid place-items-center text-slate-500">A−</button>
                    <button @click="fontScale = Math.min(1.8, fontScale + 0.1)" class="w-8 h-8 grid place-items-center font-bold text-slate-700 dark:text-slate-200">A+</button>
                </div>
            </div>

            {{-- Stop → save modal --}}
            <div x-show="showSave" x-cloak x-transition class="fixed inset-0 z-50 grid place-items-center bg-black/40 backdrop-blur-sm p-4">
                <div class="card p-6 max-w-md w-full animate-pop">
                    <div class="text-center mb-5">
                        <div class="text-4xl mb-2">🤲</div>
                        <h3 class="text-xl font-extrabold">Log this reading</h3>
                        <p class="text-sm text-slate-500">You read <span class="font-bold text-brand-600">{{ $surah['englishName'] }}</span> for <span class="font-bold text-brand-600" x-text="display"></span></p>
                    </div>
                    <form method="POST" action="{{ route('reading.store') }}" class="space-y-4">
                        @csrf
                        <input type="hidden" name="method" value="timer">
                        <input type="hidden" name="date" value="{{ now()->format('Y-m-d') }}">
                        <input type="hidden" name="surah" value="{{ $surah['englishName'] }}">
                        <input type="hidden" name="minutes_read" :value="minutes">
                        <input type="hidden" name="started_at" :value="iso(startedAt)">
                        <input type="hidden" name="ended_at" :value="iso(endedAt)">
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="form-label">Start Page</label>
                                <input type="number" name="start_page" min="1" max="604" class="form-input-field">
                            </div>
                            <div>
                                <label class="form-label">End Page</label>
                                <input type="number" name="end_page" min="1" max="604" class="form-input-field">
                            </div>
                        </div>
                        <div>
                            <label class="form-label">Notes</label>
                            <textarea name="notes" rows="2" class="form-input-field" placeholder="Optional"></textarea>
                        </div>
                        <div class="flex justify-end gap-3">
                            <button type="button" @click="showSave = false" class="btn-secondary">Back</button>
                            <button type="submit" class="btn-primary">Save Session</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        {{-- Bismillah (all surahs except At-Tawbah #9; Al-Fatihah #1 includes it as ayah 1) --}}
        @if (! in_array($surah['number'], [1, 9]))
            <div class="card p-6 text-center">
                <p class="font-quran text-3xl text-brand-700 dark:text-brand-300" dir="rtl">بِسْمِ ٱللَّهِ ٱلرَّحْمَٰنِ ٱلرَّحِيمِ</p>
                <p class="text-xs text-slate-400 mt-2">In the name of Allah, the Entirely Merciful, the Especially Merciful.</p>
            </div>
        @endif

        {{-- Ayahs --}}
        <div class="card divide-y divide-slate-100 dark:divide-slate-700/50">
            @foreach ($surah['ayahs'] as $ayah)
                <div class="p-5 sm:p-6">
                    <div class="flex items-start justify-between gap-3 mb-3">
                        <div class="w-9 h-9 shrink-0 grid place-items-center relative">
                            <svg viewBox="0 0 40 40" class="w-9 h-9 text-brand-100 dark:text-brand-900/70 absolute inset-0"><path fill="currentColor" d="M20 1l4.6 3.3 5.6-.8 2.3 5.2 5.2 2.3-.8 5.6L39 20l-3.3 4.6.8 5.6-5.2 2.3-2.3 5.2-5.6-.8L20 39l-4.6-3.3-5.6.8-2.3-5.2L2.3 29l.8-5.6L1 20l3.3-4.6-.8-5.6 5.2-2.3 2.3-5.2 5.6.8z"/></svg>
                            <span class="relative text-[11px] font-bold text-brand-700 dark:text-brand-300">{{ $surah['number'] }}:{{ $ayah['number'] }}</span>
                        </div>
                        <p class="font-quran text-2xl sm:text-3xl text-right flex-1 text-slate-800 dark:text-slate-100"
                           dir="rtl" :style="`font-size: ${1.75 * fontScale}rem`">
                            {{ $ayah['arabic'] }}
                        </p>
                    </div>
                    <p x-show="showTranslation" class="text-sm text-slate-600 dark:text-slate-300 leading-relaxed">
                        <span class="font-semibold text-brand-600">{{ $ayah['number'] }}.</span>
                        {{ $ayah['translation'] }}
                    </p>
                </div>
            @endforeach
        </div>

        {{-- Surah navigation --}}
        <div class="flex items-center justify-between gap-3">
            @if ($prev)
                <a href="{{ route('quran.show', $prev['number']) }}" class="btn-secondary flex-1 sm:flex-none">← {{ $prev['englishName'] }}</a>
            @else
                <span></span>
            @endif
            <a href="{{ route('quran.index') }}" class="btn-ghost">All Surahs</a>
            @if ($next)
                <a href="{{ route('quran.show', $next['number']) }}" class="btn-secondary flex-1 sm:flex-none text-right">{{ $next['englishName'] }} →</a>
            @else
                <span></span>
            @endif
        </div>
    </div>
</x-app-layout>
