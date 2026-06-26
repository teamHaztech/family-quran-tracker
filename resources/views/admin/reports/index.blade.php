<x-app-layout>
    <x-slot name="title">Reports</x-slot>

    @php $q = request()->only('period','from','to','member_id'); @endphp

    <div class="max-w-6xl mx-auto space-y-6">

        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
            <div>
                <h2 class="text-2xl font-extrabold">Reading Reports</h2>
                <p class="text-sm text-slate-500 dark:text-slate-400">
                    {{ ucfirst($report['period']) }} · {{ $report['from']->format('M d, Y') }} – {{ $report['to']->format('M d, Y') }}
                    @if ($report['member']) · {{ $report['member']->fullName() }} @endif
                </p>
            </div>
            <div class="flex gap-2">
                <a href="{{ route('admin.reports.export', array_merge($q, ['format'=>'csv'])) }}" class="btn-secondary">⬇ CSV</a>
                <a href="{{ route('admin.reports.export', array_merge($q, ['format'=>'xlsx'])) }}" class="btn-secondary">⬇ Excel</a>
                <a href="{{ route('admin.reports.export', array_merge($q, ['format'=>'pdf'])) }}" class="btn-primary">⬇ PDF</a>
            </div>
        </div>

        {{-- Filters --}}
        <form method="GET" class="card p-4 grid sm:grid-cols-4 gap-3" x-data="{ period: '{{ $report['period'] }}' }">
            <div>
                <label class="form-label">Period</label>
                <select name="period" x-model="period" class="form-input-field">
                    @foreach (['daily'=>'Daily','weekly'=>'Weekly','monthly'=>'Monthly','yearly'=>'Yearly','custom'=>'Custom Range'] as $v=>$l)
                        <option value="{{ $v }}" @selected($report['period']===$v)>{{ $l }}</option>
                    @endforeach
                </select>
            </div>
            <div x-show="period==='custom'" x-cloak>
                <label class="form-label">From</label>
                <input type="date" name="from" value="{{ request('from') }}" class="form-input-field">
            </div>
            <div x-show="period==='custom'" x-cloak>
                <label class="form-label">To</label>
                <input type="date" name="to" value="{{ request('to') }}" class="form-input-field">
            </div>
            <div>
                <label class="form-label">Member</label>
                <select name="member_id" class="form-input-field">
                    <option value="">All members</option>
                    @foreach ($members as $m)
                        <option value="{{ $m->id }}" @selected(request('member_id')==$m->id)>{{ $m->fullName() }}</option>
                    @endforeach
                </select>
            </div>
            <div class="sm:col-span-4 flex justify-end">
                <button class="btn-primary">Generate Report</button>
            </div>
        </form>

        {{-- Summary --}}
        <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
            <x-stat-card label="Total Pages" :value="$report['summary']['total_pages']" icon="📖" accent="brand" :animate="false" />
            <x-stat-card label="Total Minutes" :value="$report['summary']['total_minutes']" icon="⏱️" accent="blue" :animate="false" />
            <x-stat-card label="Sessions" :value="$report['summary']['total_sessions']" icon="✅" accent="violet" :animate="false" />
            <x-stat-card label="Active Readers" :value="$report['summary']['active_readers']" icon="👥" accent="gold" :animate="false" />
        </div>

        {{-- Per-member breakdown --}}
        @if ($report['by_member']->isNotEmpty())
            <div class="card p-6">
                <h3 class="font-bold mb-4">By Member</h3>
                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead class="text-left text-slate-400 border-b border-slate-100 dark:border-slate-700">
                            <tr><th class="py-2 font-medium">Member</th><th class="py-2 font-medium text-right">Pages</th><th class="py-2 font-medium text-right">Minutes</th><th class="py-2 font-medium text-right">Sessions</th></tr>
                        </thead>
                        <tbody class="divide-y divide-slate-50 dark:divide-slate-700/50">
                            @foreach ($report['by_member'] as $row)
                                <tr>
                                    <td class="py-2.5 font-medium">{{ $row['name'] }}</td>
                                    <td class="py-2.5 text-right font-bold text-brand-600">{{ $row['pages'] }}</td>
                                    <td class="py-2.5 text-right">{{ $row['minutes'] }}</td>
                                    <td class="py-2.5 text-right">{{ $row['sessions'] }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        @endif

        {{-- Detailed sessions --}}
        <div class="card p-6">
            <h3 class="font-bold mb-4">Detailed Sessions ({{ $report['rows']->count() }})</h3>
            @if ($report['rows']->isEmpty())
                <x-empty-state icon="📭" title="No data for this period" message="Try a different period or member." />
            @else
                <div class="overflow-x-auto max-h-[28rem]">
                    <table class="w-full text-sm">
                        <thead class="text-left text-slate-400 border-b border-slate-100 dark:border-slate-700 sticky top-0 bg-white dark:bg-slate-800">
                            <tr><th class="py-2 font-medium">Date</th><th class="py-2 font-medium">Member</th><th class="py-2 font-medium">Surah</th><th class="py-2 font-medium text-right">Pages</th><th class="py-2 font-medium text-right">Min</th></tr>
                        </thead>
                        <tbody class="divide-y divide-slate-50 dark:divide-slate-700/50">
                            @foreach ($report['rows'] as $s)
                                <tr>
                                    <td class="py-2.5">{{ $s->date->format('M d') }}</td>
                                    <td class="py-2.5">{{ $s->user?->fullName() }}</td>
                                    <td class="py-2.5">{{ $s->surah ?? '—' }}</td>
                                    <td class="py-2.5 text-right font-semibold text-brand-600">{{ $s->pages_read }}</td>
                                    <td class="py-2.5 text-right">{{ $s->minutes_read }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
