<x-app-layout>
    <x-slot name="title">Reading Timer</x-slot>

    <div class="max-w-2xl mx-auto space-y-6" x-data="readingTimer">

        {{-- Method switcher --}}
        <div class="grid grid-cols-2 gap-3">
            <a href="{{ route('reading.create') }}" class="card card-hover p-4 flex items-center gap-3">
                <span class="w-10 h-10 rounded-xl bg-slate-100 dark:bg-slate-700 grid place-items-center text-xl">✍️</span>
                <div>
                    <p class="font-bold text-sm">Manual Entry</p>
                    <p class="text-xs text-slate-400">Log a past session</p>
                </div>
            </a>
            <div class="card p-4 border-2 !border-brand-500 flex items-center gap-3">
                <span class="w-10 h-10 rounded-xl bg-brand-100 dark:bg-brand-900/40 grid place-items-center text-xl">⏱️</span>
                <div>
                    <p class="font-bold text-sm">Reading Timer</p>
                    <p class="text-xs text-slate-400">Read &amp; track live</p>
                </div>
            </div>
        </div>

        {{-- Timer card --}}
        <div class="card p-8 text-center bg-gradient-to-br from-brand-600 to-brand-800 text-white relative overflow-hidden">
            <div class="absolute -top-16 -right-16 w-56 h-56 rounded-full bg-white/5"></div>
            <div class="absolute -bottom-20 -left-10 w-56 h-56 rounded-full bg-white/5"></div>

            <p class="relative text-brand-100 text-sm font-medium uppercase tracking-wider mb-4">
                <span x-show="!running && seconds === 0">Ready when you are</span>
                <span x-show="running" x-cloak>Reading in progress…</span>
                <span x-show="!running && seconds > 0" x-cloak>Session paused</span>
            </p>

            <div class="relative text-6xl sm:text-7xl font-extrabold tabular-nums tracking-tight mb-6" x-text="display">00:00:00</div>

            <div class="relative flex items-center justify-center gap-3">
                <button x-show="!running" @click="start()" class="btn bg-white text-brand-700 hover:bg-brand-50 !px-8 !py-3 text-base shadow-soft-lg">
                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M8 5v14l11-7z"/></svg>
                    <span x-text="seconds > 0 ? 'Resume' : 'Start Reading'">Start Reading</span>
                </button>
                <button x-show="running" x-cloak @click="stop()" class="btn bg-white text-red-600 hover:bg-red-50 !px-8 !py-3 text-base shadow-soft-lg">
                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><rect x="6" y="6" width="12" height="12" rx="2"/></svg>
                    Stop &amp; Save
                </button>
                <button x-show="!running && seconds > 0" x-cloak @click="reset()" class="btn bg-white/15 text-white hover:bg-white/25 !px-5 !py-3">Reset</button>
            </div>
        </div>

        <p class="text-center text-xs text-slate-400">
            📖 Open your Mushaf and start the timer. When you finish, we'll save your duration automatically.
        </p>

        {{-- Save modal --}}
        <div x-show="showSave" x-cloak x-transition class="fixed inset-0 z-50 grid place-items-center bg-black/40 backdrop-blur-sm p-4">
            <div class="card p-6 sm:p-8 max-w-md w-full animate-pop">
                <div class="text-center mb-5">
                    <div class="text-4xl mb-2">🤲</div>
                    <h3 class="text-xl font-extrabold">Save your session</h3>
                    <p class="text-sm text-slate-500">You read for <span class="font-bold text-brand-600" x-text="display"></span> (<span x-text="minutes"></span> min)</p>
                </div>

                <form method="POST" action="{{ route('reading.store') }}" class="space-y-4">
                    @csrf
                    <input type="hidden" name="method" value="timer">
                    <input type="hidden" name="date" value="{{ now()->format('Y-m-d') }}">
                    <input type="hidden" name="minutes_read" :value="minutes">
                    <input type="hidden" name="started_at" :value="iso(startedAt)">
                    <input type="hidden" name="ended_at" :value="iso(endedAt)">

                    <div>
                        <label class="form-label">Surah</label>
                        <input type="text" name="surah" class="form-input-field" placeholder="e.g. Al-Kahf" list="surah-list">
                    </div>
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

                    <div class="flex justify-end gap-3 pt-1">
                        <button type="button" @click="showSave = false" class="btn-secondary">Back</button>
                        <button type="submit" class="btn-primary">Save Session</button>
                    </div>
                </form>
            </div>
        </div>

        <datalist id="surah-list">
            @foreach (['Al-Fatihah','Al-Baqarah','Aal-i-Imran','An-Nisa','Al-Maidah','Al-Anam','Al-Araf','Al-Kahf','Maryam','Ya-Sin','Ar-Rahman','Al-Waqiah','Al-Mulk','An-Naba','Al-Ikhlas'] as $s)
                <option value="{{ $s }}">
            @endforeach
        </datalist>
    </div>
</x-app-layout>
