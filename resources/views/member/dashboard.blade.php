<x-app-layout>
    <x-slot name="title">Dashboard</x-slot>

    <div class="max-w-6xl mx-auto space-y-6">

        {{-- Greeting + reminder --}}
        <div class="card p-6 bg-gradient-to-br from-brand-600 to-brand-800 text-white relative overflow-hidden">
            <div class="absolute -top-12 -right-8 w-48 h-48 rounded-full bg-white/5"></div>
            <div class="relative flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                <div>
                    <p class="text-brand-100 text-sm">As-salamu alaykum,</p>
                    <h2 class="text-2xl font-extrabold">{{ auth()->user()->first_name }} 🌙</h2>
                    @if (! $data['logged_today'])
                        <p class="mt-2 text-sm text-brand-50/90">You haven't logged today's reading yet. Keep your streak of {{ $data['current_streak'] }} days alive!</p>
                    @else
                        <p class="mt-2 text-sm text-brand-50/90">Maa shaa Allah — you've read {{ $data['today_pages'] }} pages today. 🤲</p>
                    @endif
                </div>
                <div class="flex gap-2">
                    <a href="{{ route('reading.timer') }}" class="btn bg-white text-brand-700 hover:bg-brand-50">⏱️ Read Now</a>
                    <a href="{{ route('reading.create') }}" class="btn bg-white/15 text-white hover:bg-white/25">✍️ Log</a>
                </div>
            </div>
        </div>

        {{-- Progress rings --}}
        <div class="grid sm:grid-cols-2 gap-4">
            <div class="card p-6 flex items-center gap-6">
                <x-progress-ring :percent="$data['daily_goal_pct']" :size="110" />
                <div>
                    <p class="text-sm font-semibold text-slate-500 dark:text-slate-400">Today's Goal</p>
                    <p class="text-2xl font-extrabold">{{ $data['today_pages'] }} / {{ $data['daily_goal'] }} <span class="text-sm font-normal text-slate-400">pages</span></p>
                    <p class="text-xs text-slate-400 mt-1">{{ $data['today_minutes'] }} minutes read today</p>
                </div>
            </div>
            <div class="card p-6 flex items-center gap-6">
                <x-progress-ring :percent="$data['monthly_goal_pct']" :size="110" />
                <div>
                    <p class="text-sm font-semibold text-slate-500 dark:text-slate-400">Monthly Goal</p>
                    <p class="text-2xl font-extrabold">{{ $data['month_pages'] }} / {{ $data['monthly_goal'] }} <span class="text-sm font-normal text-slate-400">pages</span></p>
                    <p class="text-xs text-slate-400 mt-1">{{ $data['month_minutes'] }} minutes this month</p>
                </div>
            </div>
        </div>

        {{-- Stat cards --}}
        <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
            <x-stat-card label="Current Streak" :value="$data['current_streak']" suffix="days in a row" icon="🔥" accent="gold" />
            <x-stat-card label="Longest Streak" :value="$data['longest_streak']" suffix="days record" icon="⚡" accent="violet" />
            <x-stat-card label="Total Pages" :value="$data['total_pages']" suffix="all time" icon="📖" accent="brand" />
            <x-stat-card label="Reading Time" :value="$data['total_minutes']" suffix="minutes" icon="⏱️" accent="blue" />
        </div>

        {{-- Charts --}}
        <div class="grid lg:grid-cols-3 gap-4">
            <div class="card p-6 lg:col-span-2">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="font-bold">Daily Pages — Last 30 Days</h3>
                    <span class="pill-success">{{ $data['week_pages'] }} this week</span>
                </div>
                <div class="h-64"><canvas id="dailyChart"></canvas></div>
            </div>
            <div class="card p-6">
                <h3 class="font-bold mb-4">Weekly Activity</h3>
                <div class="h-64"><canvas id="weeklyChart"></canvas></div>
            </div>
        </div>

        {{-- Heatmap + Recent --}}
        <div class="grid lg:grid-cols-3 gap-4">
            <div class="card p-6 lg:col-span-2">
                <h3 class="font-bold mb-4">Reading Calendar</h3>
                <x-heatmap :data="$heatmap" />
            </div>

            <div class="card p-6">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="font-bold">Recent Sessions</h3>
                    <a href="{{ route('reading.index') }}" class="text-xs font-medium text-brand-600">View all</a>
                </div>
                @if ($recent->isEmpty())
                    <x-empty-state icon="📭" title="No sessions yet" message="Log your first reading." class="!py-8" />
                @else
                    <div class="space-y-3">
                        @foreach ($recent as $s)
                            <div class="flex items-center gap-3">
                                <div class="w-9 h-9 rounded-xl bg-brand-50 dark:bg-brand-900/30 grid place-items-center text-xs font-bold text-brand-600 shrink-0">
                                    {{ $s->date->format('d') }}
                                </div>
                                <div class="flex-1 min-w-0">
                                    <p class="text-sm font-medium truncate">{{ $s->surah ?? 'Quran Reading' }}</p>
                                    <p class="text-xs text-slate-400">{{ $s->date->format('M d') }} · {{ $s->minutes_read }} min</p>
                                </div>
                                <span class="text-sm font-bold text-brand-600">{{ $s->pages_read }}pg</span>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>
        </div>

        {{-- Badges --}}
        @if ($badges->isNotEmpty())
            <div class="card p-6">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="font-bold">Your Achievements 🏅</h3>
                    <a href="{{ route('badges.index') }}" class="text-xs font-medium text-brand-600">View all</a>
                </div>
                <div class="flex flex-wrap gap-4">
                    @foreach ($badges as $b)
                        <div class="flex flex-col items-center gap-1.5 w-20 text-center">
                            <div class="w-14 h-14 rounded-2xl bg-gradient-to-br from-gold-400/20 to-brand-500/20 grid place-items-center text-2xl">{{ $b->icon }}</div>
                            <span class="text-[11px] font-medium leading-tight">{{ $b->name }}</span>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif
    </div>

    @push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            renderChart('dailyChart', 'line', @json($dailyPages['labels']), @json($dailyPages['data']), { label: 'Pages' });
            renderChart('weeklyChart', 'bar', @json($weeklyActivity['labels']), @json($weeklyActivity['data']), { label: 'Pages' });
        });
    </script>
    @endpush
</x-app-layout>
