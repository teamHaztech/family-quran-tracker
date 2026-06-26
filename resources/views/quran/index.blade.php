<x-app-layout>
    <x-slot name="title">Read Quran</x-slot>

    <div class="max-w-5xl mx-auto space-y-6">

        {{-- Header --}}
        <div class="card p-6 bg-gradient-to-br from-brand-700 to-brand-900 text-white relative overflow-hidden">
            <div class="absolute -top-16 -right-10 w-56 h-56 rounded-full bg-white/5"></div>
            <div class="relative">
                <h2 class="text-2xl font-extrabold">The Holy Qur'an</h2>
                <p class="text-brand-100 text-sm mt-1">All 114 surahs · Arabic (Uthmani) with English translation</p>
                <p class="text-brand-100/80 text-xs mt-3">114 Surahs · 6,236 Ayahs · 30 Juz</p>
            </div>
        </div>

        {{-- Search --}}
        <form method="GET" class="card p-3">
            <div class="relative">
                <svg class="w-5 h-5 absolute left-3 top-1/2 -translate-y-1/2 text-slate-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-4.35-4.35M11 19a8 8 0 100-16 8 8 0 000 16z"/></svg>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Search surah by name or number…" class="form-input-field !pl-10">
            </div>
        </form>

        {{-- Surah grid --}}
        @if ($surahs->isEmpty())
            <div class="card"><x-empty-state icon="🔍" title="No surah found" message="Try a different name or number." /></div>
        @else
            <div class="grid sm:grid-cols-2 lg:grid-cols-3 gap-3">
                @foreach ($surahs as $s)
                    <a href="{{ route('quran.show', $s['number']) }}" class="card card-hover p-4 flex items-center gap-3">
                        <div class="w-11 h-11 shrink-0 grid place-items-center relative">
                            <svg viewBox="0 0 40 40" class="w-11 h-11 text-brand-200 dark:text-brand-900 absolute inset-0"><path fill="currentColor" d="M20 1l4.6 3.3 5.6-.8 2.3 5.2 5.2 2.3-.8 5.6L39 20l-3.3 4.6.8 5.6-5.2 2.3-2.3 5.2-5.6-.8L20 39l-4.6-3.3-5.6.8-2.3-5.2L2.3 29l.8-5.6L1 20l3.3-4.6-.8-5.6 5.2-2.3 2.3-5.2 5.6.8z"/></svg>
                            <span class="relative text-xs font-extrabold text-brand-700 dark:text-brand-300">{{ $s['number'] }}</span>
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="font-bold truncate">{{ $s['englishName'] }}</p>
                            <p class="text-xs text-slate-400 truncate">{{ $s['englishTranslation'] }} · {{ $s['numberOfAyahs'] }} ayahs · {{ $s['revelationType'] }}</p>
                        </div>
                        <span class="font-quran text-xl text-brand-700 dark:text-brand-300 shrink-0" dir="rtl">{{ \Illuminate\Support\Str::of($s['name'])->replace('سُورَةُ ', '') }}</span>
                    </a>
                @endforeach
            </div>
        @endif
    </div>
</x-app-layout>
