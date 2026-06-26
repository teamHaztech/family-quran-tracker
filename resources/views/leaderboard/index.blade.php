<x-app-layout>
    <x-slot name="title">Leaderboard</x-slot>

    <div class="max-w-3xl mx-auto space-y-6">

        <div class="text-center">
            <h2 class="text-2xl font-extrabold">🏆 Family Leaderboard</h2>
            <p class="text-sm text-slate-500 dark:text-slate-400">Friendly competition in the remembrance of Allah.</p>
        </div>

        {{-- Period tabs --}}
        <div class="flex justify-center">
            <div class="card p-1 inline-flex gap-1">
                @foreach (['weekly'=>'This Week','monthly'=>'This Month','all'=>'All Time'] as $p => $label)
                    <a href="{{ route('leaderboard.index', ['period' => $p]) }}"
                       class="px-4 py-2 rounded-xl text-sm font-semibold transition {{ $period===$p ? 'bg-brand-600 text-white shadow-soft' : 'text-slate-500 hover:bg-slate-100 dark:hover:bg-slate-700' }}">
                        {{ $label }}
                    </a>
                @endforeach
            </div>
        </div>

        @if ($rankings->isEmpty())
            <div class="card"><x-empty-state icon="📊" title="No data yet" message="No reading recorded for this period." /></div>
        @else
            {{-- Podium (top 3) --}}
            @php $top = $rankings->take(3); @endphp
            <div class="grid grid-cols-3 gap-3 items-end">
                @foreach ([1 => $top->get(1), 0 => $top->get(0), 2 => $top->get(2)] as $pos => $m)
                    @continue(! $m)
                    @php
                        $rank = $rankings->search(fn($x) => $x->id === $m->id) + 1;
                        $heights = [1 => 'h-28', 0 => 'h-36', 2 => 'h-24'];
                        $medals = [0 => '🥇', 1 => '🥈', 2 => '🥉'];
                    @endphp
                    <div class="flex flex-col items-center">
                        <img src="{{ $m->photo ? \Illuminate\Support\Facades\Storage::disk('public')->url($m->photo) : 'https://ui-avatars.com/api/?background=047857&color=fff&name='.urlencode($m->name) }}"
                             class="w-14 h-14 rounded-full object-cover ring-4 ring-white dark:ring-slate-800 -mb-7 z-10" alt="">
                        <div class="card {{ $heights[$pos] }} w-full flex flex-col items-center justify-end p-3 pt-9 text-center {{ $pos===0 ? 'bg-gradient-to-b from-gold-400/20 to-transparent' : '' }}">
                            <span class="text-2xl">{{ $medals[$pos] }}</span>
                            <p class="text-xs font-bold truncate w-full">{{ $m->name }}</p>
                            <p class="text-brand-600 font-extrabold">{{ (int) $m->total_pages }}</p>
                        </div>
                    </div>
                @endforeach
            </div>

            {{-- Full ranking --}}
            <div class="card divide-y divide-slate-50 dark:divide-slate-700/50">
                @foreach ($rankings as $i => $m)
                    @php $isMe = $m->id === auth()->id(); @endphp
                    <div class="p-4 flex items-center gap-4 {{ $isMe ? 'bg-brand-50/60 dark:bg-brand-900/20' : '' }}">
                        <span class="w-7 text-center font-bold {{ $i < 3 ? 'text-gold-500' : 'text-slate-400' }}">{{ $i + 1 }}</span>
                        <img src="{{ $m->photo ? \Illuminate\Support\Facades\Storage::disk('public')->url($m->photo) : 'https://ui-avatars.com/api/?background=047857&color=fff&name='.urlencode($m->name) }}"
                             class="w-10 h-10 rounded-full object-cover" alt="">
                        <div class="flex-1 min-w-0">
                            <p class="font-semibold text-sm truncate">{{ $m->name }} @if($isMe)<span class="pill-success ml-1">You</span>@endif</p>
                            <p class="text-xs text-slate-400">{{ (int) $m->total_minutes }} minutes read</p>
                        </div>
                        <p class="font-extrabold text-brand-600">{{ (int) $m->total_pages }} <span class="text-xs font-normal text-slate-400">pg</span></p>
                    </div>
                @endforeach
            </div>
        @endif
    </div>
</x-app-layout>
