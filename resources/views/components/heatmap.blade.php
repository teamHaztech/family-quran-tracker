@props([
    'data' => [],     // ['Y-m-d' => pages]
    'weeks' => 17,
])

@php
    use Illuminate\Support\Carbon;

    $end = Carbon::today();
    // Start on the Sunday that begins the grid.
    $start = $end->copy()->subWeeks($weeks - 1)->startOfWeek(Carbon::SUNDAY);

    $days = [];
    $cursor = $start->copy();
    while ($cursor->lte($end)) {
        $key = $cursor->toDateString();
        $days[] = ['date' => $cursor->copy(), 'pages' => $data[$key] ?? 0];
        $cursor->addDay();
    }
    $columns = array_chunk($days, 7);

    $level = function ($pages) {
        if ($pages <= 0) return 'bg-slate-100 dark:bg-slate-700/60';
        if ($pages < 3) return 'bg-brand-200 dark:bg-brand-900';
        if ($pages < 6) return 'bg-brand-300 dark:bg-brand-700';
        if ($pages < 10) return 'bg-brand-500 dark:bg-brand-600';
        return 'bg-brand-700 dark:bg-brand-400';
    };
@endphp

<div {{ $attributes }}>
    <div class="flex gap-1 overflow-x-auto pb-1">
        @foreach ($columns as $week)
            <div class="flex flex-col gap-1">
                @foreach ($week as $day)
                    <div class="w-3.5 h-3.5 rounded-sm {{ $level($day['pages']) }}"
                         title="{{ $day['date']->format('M d, Y') }} · {{ $day['pages'] }} pages"></div>
                @endforeach
            </div>
        @endforeach
    </div>
    <div class="flex items-center gap-1.5 mt-3 text-[11px] text-slate-400">
        <span>Less</span>
        <span class="w-3 h-3 rounded-sm bg-slate-100 dark:bg-slate-700/60"></span>
        <span class="w-3 h-3 rounded-sm bg-brand-200 dark:bg-brand-900"></span>
        <span class="w-3 h-3 rounded-sm bg-brand-300 dark:bg-brand-700"></span>
        <span class="w-3 h-3 rounded-sm bg-brand-500 dark:bg-brand-600"></span>
        <span class="w-3 h-3 rounded-sm bg-brand-700 dark:bg-brand-400"></span>
        <span>More</span>
    </div>
</div>
