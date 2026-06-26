<x-app-layout>
    <x-slot name="title">Achievements</x-slot>

    <div class="max-w-4xl mx-auto space-y-6">

        <div class="text-center">
            <h2 class="text-2xl font-extrabold">🏅 Achievements</h2>
            <p class="text-sm text-slate-500 dark:text-slate-400">
                You've earned {{ count($earnedIds) }} of {{ $allBadges->count() }} badges. Keep going!
            </p>
        </div>

        <div class="grid sm:grid-cols-2 lg:grid-cols-3 gap-4">
            @foreach ($allBadges as $badge)
                @php $earned = in_array($badge->id, $earnedIds); @endphp
                <div class="card p-6 text-center {{ $earned ? 'card-hover' : 'opacity-60' }} relative">
                    @if ($earned)
                        <span class="absolute top-3 right-3 pill-success">Earned ✓</span>
                    @else
                        <span class="absolute top-3 right-3 pill-muted">Locked 🔒</span>
                    @endif

                    <div class="w-20 h-20 mx-auto rounded-3xl grid place-items-center text-4xl mb-3
                                {{ $earned ? 'bg-gradient-to-br from-gold-400/25 to-brand-500/25 animate-pop' : 'bg-slate-100 dark:bg-slate-700 grayscale' }}">
                        {{ $badge->icon }}
                    </div>
                    <h3 class="font-bold">{{ $badge->name }}</h3>
                    <p class="text-xs text-slate-500 dark:text-slate-400 mt-1">{{ $badge->description }}</p>
                </div>
            @endforeach
        </div>
    </div>
</x-app-layout>
