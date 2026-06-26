<x-app-layout>
    <x-slot name="title">Dashboard</x-slot>

    <div class="max-w-7xl mx-auto space-y-6">

        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
            <div>
                <h2 class="text-2xl font-extrabold">Family Overview</h2>
                <p class="text-sm text-slate-500 dark:text-slate-400">{{ now()->format('l, F j, Y') }}</p>
            </div>
            <div class="flex gap-2">
                <a href="{{ route('admin.members.create') }}" class="btn-secondary">+ Member</a>
                <a href="{{ route('admin.reports.index') }}" class="btn-primary">View Reports</a>
            </div>
        </div>

        {{-- Primary stat cards --}}
        <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
            <x-stat-card label="Family Members" :value="$data['total_members']" suffix="{{ $data['active_members'] }} active" icon="👨‍👩‍👧‍👦" accent="brand" />
            <x-stat-card label="Today's Pages" :value="$data['today_pages']" suffix="{{ $data['today_minutes'] }} min read" icon="📖" accent="gold" />
            <x-stat-card label="This Week" :value="$data['week_pages']" suffix="pages read" icon="📅" accent="blue" />
            <x-stat-card label="This Month" :value="$data['month_pages']" suffix="{{ $data['month_minutes'] }} min" icon="🗓️" accent="violet" />
        </div>

        <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
            <x-stat-card label="Total Pages" :value="$data['total_pages']" suffix="all time" icon="📚" accent="brand" />
            <x-stat-card label="Total Sessions" :value="$data['total_sessions']" suffix="recorded" icon="✅" accent="blue" />
            <x-stat-card label="Best Family Streak" :value="$data['family_streak']" suffix="days" icon="🔥" accent="gold" />
            <x-stat-card label="Badges Awarded" :value="$data['badges_awarded']" suffix="earned" icon="🏅" accent="violet" />
        </div>

        {{-- Most / Least active --}}
        <div class="grid sm:grid-cols-2 gap-4">
            @if ($data['most_active'])
                <div class="card p-5 flex items-center gap-4">
                    <div class="w-12 h-12 rounded-2xl bg-gold-400/20 grid place-items-center text-2xl">🏆</div>
                    <div class="flex-1">
                        <p class="text-xs text-slate-400 uppercase tracking-wide">Most Active</p>
                        <p class="font-bold">{{ $data['most_active']->name }}</p>
                    </div>
                    <p class="text-2xl font-extrabold text-brand-600">{{ (int) $data['most_active']->total_pages }}<span class="text-xs font-normal text-slate-400">pg</span></p>
                </div>
            @endif
            @if ($data['least_active'])
                <div class="card p-5 flex items-center gap-4">
                    <div class="w-12 h-12 rounded-2xl bg-slate-200/60 dark:bg-slate-700 grid place-items-center text-2xl">💤</div>
                    <div class="flex-1">
                        <p class="text-xs text-slate-400 uppercase tracking-wide">Needs Encouragement</p>
                        <p class="font-bold">{{ $data['least_active']->name }}</p>
                    </div>
                    <p class="text-2xl font-extrabold text-slate-400">{{ (int) $data['least_active']->total_pages }}<span class="text-xs font-normal">pg</span></p>
                </div>
            @endif
        </div>

        {{-- Charts row 1 --}}
        <div class="grid lg:grid-cols-3 gap-4">
            <div class="card p-6 lg:col-span-2">
                <h3 class="font-bold mb-4">Daily Pages — Family (Last 30 Days)</h3>
                <div class="h-64"><canvas id="dailyChart"></canvas></div>
            </div>
            <div class="card p-6">
                <h3 class="font-bold mb-4">Top Readers</h3>
                <div class="h-64"><canvas id="topChart"></canvas></div>
            </div>
        </div>

        {{-- Charts row 2 --}}
        <div class="grid lg:grid-cols-2 gap-4">
            <div class="card p-6">
                <h3 class="font-bold mb-4">Weekly Reading Activity</h3>
                <div class="h-60"><canvas id="weeklyChart"></canvas></div>
            </div>
            <div class="card p-6">
                <h3 class="font-bold mb-4">Monthly Reading Time (minutes)</h3>
                <div class="h-60"><canvas id="monthlyChart"></canvas></div>
            </div>
        </div>

        {{-- Heatmap --}}
        <div class="card p-6">
            <h3 class="font-bold mb-4">Family Reading Heatmap</h3>
            <x-heatmap :data="$heatmap" />
        </div>
    </div>

    @push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            renderChart('dailyChart', 'line', @json($dailyPages['labels']), @json($dailyPages['data']), { label: 'Pages' });
            renderChart('topChart', 'doughnut', @json($topReaders['labels']), @json($topReaders['data']), { label: 'Pages' });
            renderChart('weeklyChart', 'bar', @json($weeklyActivity['labels']), @json($weeklyActivity['data']), { label: 'Pages' });
            renderChart('monthlyChart', 'line', @json($monthlyTime['labels']), @json($monthlyTime['data']), { label: 'Minutes' });
        });
    </script>
    @endpush
</x-app-layout>
