<x-app-layout>
    <x-slot name="title">Reading History</x-slot>

    <div class="max-w-5xl mx-auto space-y-6">

        <div class="flex items-center justify-between gap-4">
            <div>
                <h2 class="text-2xl font-extrabold">Reading History</h2>
                <p class="text-sm text-slate-500 dark:text-slate-400">Every page you've recorded.</p>
            </div>
            <a href="{{ route('reading.create') }}" class="btn-primary self-start">+ Log Reading</a>
        </div>

        {{-- Today's record / reminder --}}
        @if ($today)
            <div class="card p-4 flex items-center gap-3 border-l-4 !border-l-brand-500">
                <span class="text-2xl">✅</span>
                <div class="flex-1">
                    <p class="text-sm font-semibold">You've logged today — {{ $today->pages_read }} pages in {{ $today->minutes_read }} min</p>
                    <p class="text-xs text-slate-400">{{ $today->surah ?? 'Quran reading' }}</p>
                </div>
                <a href="{{ route('reading.edit', $today) }}" class="btn-secondary !py-1.5 !px-3 text-xs">Edit today</a>
            </div>
        @else
            <div class="card p-4 flex items-center gap-3 border-l-4 !border-l-gold-500">
                <span class="text-2xl">🕮</span>
                <div class="flex-1">
                    <p class="text-sm font-semibold">You haven't logged today's reading yet.</p>
                    <p class="text-xs text-slate-400">Keep your streak alive!</p>
                </div>
                <a href="{{ route('reading.timer') }}" class="btn-primary !py-1.5 !px-3 text-xs">Read now</a>
            </div>
        @endif

        {{-- Filters --}}
        <form method="GET" class="card p-3 grid sm:grid-cols-4 gap-3">
            <div class="relative sm:col-span-2">
                <svg class="w-5 h-5 absolute left-3 top-1/2 -translate-y-1/2 text-slate-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-4.35-4.35M11 19a8 8 0 100-16 8 8 0 000 16z"/></svg>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Search surah or notes…" class="form-input-field !pl-10">
            </div>
            <input type="date" name="from" value="{{ request('from') }}" class="form-input-field" title="From date">
            <input type="date" name="to" value="{{ request('to') }}" class="form-input-field" title="To date">
            <div class="sm:col-span-4 flex flex-wrap gap-2">
                @foreach (['daily'=>'Today','weekly'=>'This Week','monthly'=>'This Month'] as $p => $label)
                    <a href="{{ route('reading.index', ['period' => $p]) }}"
                       class="pill {{ request('period')===$p ? 'bg-brand-600 text-white' : 'pill-muted' }}">{{ $label }}</a>
                @endforeach
                <button class="btn-secondary !py-1.5 ml-auto">Apply</button>
                @if (request()->hasAny(['search','from','to','period']))
                    <a href="{{ route('reading.index') }}" class="btn-ghost !py-1.5">Clear</a>
                @endif
            </div>
        </form>

        {{-- List --}}
        @if ($history->isEmpty())
            <div class="card">
                <x-empty-state icon="📖" title="No reading sessions found"
                               message="Start logging your Quran reading to build your history.">
                    <x-slot name="action"><a href="{{ route('reading.create') }}" class="btn-primary">Log your first session</a></x-slot>
                </x-empty-state>
            </div>
        @else
            <div class="card divide-y divide-slate-50 dark:divide-slate-700/50">
                @foreach ($history as $s)
                    <div class="p-4 flex items-center gap-4 hover:bg-slate-50/60 dark:hover:bg-slate-700/30 transition">
                        <div class="w-12 h-12 rounded-2xl bg-brand-50 dark:bg-brand-900/30 grid place-items-center shrink-0 text-center">
                            <span class="text-xs font-bold text-brand-600 leading-none">{{ $s->date->format('d') }}</span>
                            <span class="text-[10px] text-slate-400 uppercase">{{ $s->date->format('M') }}</span>
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="font-semibold text-sm truncate">
                                {{ $s->surah ?? 'Quran Reading' }}
                                @if ($s->method === 'timer') <span class="pill-muted ml-1">⏱️ timer</span> @endif
                            </p>
                            <p class="text-xs text-slate-400">
                                @if ($s->start_page && $s->end_page) Pages {{ $s->start_page }}–{{ $s->end_page }} · @endif
                                {{ $s->date->format('D, M d') }}
                                @if ($s->notes) · {{ Str::limit($s->notes, 40) }} @endif
                            </p>
                        </div>
                        <div class="text-right shrink-0">
                            <p class="font-extrabold text-brand-600">{{ $s->pages_read }} <span class="text-xs font-normal text-slate-400">pg</span></p>
                            <p class="text-xs text-slate-400">{{ $s->minutes_read }} min</p>
                        </div>
                        <a href="{{ route('reading.edit', $s) }}" class="w-8 h-8 rounded-lg grid place-items-center hover:bg-slate-200 dark:hover:bg-slate-600 text-slate-400 shrink-0">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                        </a>
                    </div>
                @endforeach
            </div>

            <div>{{ $history->links() }}</div>
        @endif
    </div>
</x-app-layout>
