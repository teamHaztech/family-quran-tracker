@php
    $settings = \App\Models\Setting::current();
    // icon paths (heroicons-style, stroke)
    $icons = [
        'home'   => 'M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6',
        'users'  => 'M17 20h5v-2a4 4 0 00-3-3.87M9 20H4v-2a4 4 0 013-3.87m6-1.13a4 4 0 10-4-4 4 4 0 004 4z',
        'book'   => 'M12 6.25C10.5 5 8 4.5 5 4.75v12.5C8 17 10.5 17.5 12 18.75M12 6.25C13.5 5 16 4.5 19 4.75v12.5C16 17 13.5 17.5 12 18.75M12 6.25v12.5',
        'clock'  => 'M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z',
        'chart'  => 'M9 19v-6m4 6V9m4 10V5M5 19h14',
        'trophy' => 'M8 21h8m-4-4v4m5-16h2a2 2 0 010 4h-.5M7 5H5a2 2 0 000 4h.5M7 4h10v5a5 5 0 01-10 0V4z',
        'medal'  => 'M12 15a4 4 0 100-8 4 4 0 000 8zm0 0v6m-3-3h6M9 3l3 4 3-4',
        'quran'  => 'M12 6.042A8.967 8.967 0 006 3.75c-1.052 0-2.062.18-3 .512v14.25A8.987 8.987 0 016 18c2.305 0 4.408.867 6 2.292m0-14.25a8.966 8.966 0 016-2.292c1.052 0 2.062.18 3 .512v14.25A8.987 8.987 0 0018 18a8.967 8.967 0 00-6 2.292m0-14.25v14.25',
        'doc'    => 'M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z',
        'cog'    => 'M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065zM15 12a3 3 0 11-6 0 3 3 0 016 0z',
        'list'   => 'M4 6h16M4 10h16M4 14h16M4 18h16',
    ];
@endphp

@php
    if (auth()->user()->isAdmin()) {
        $links = [
            ['admin.dashboard', 'Dashboard', 'home'],
            ['admin.members.index', 'Members', 'users'],
            ['quran.index', 'Read Quran', 'quran'],
            ['admin.reports.index', 'Reports', 'doc'],
            ['admin.activity.index', 'Activity Log', 'list'],
            ['admin.settings.edit', 'Settings', 'cog'],
        ];
    } else {
        $links = [
            ['member.dashboard', 'Dashboard', 'home'],
            ['quran.index', 'Read Quran', 'quran'],
            ['reading.create', 'Log Reading', 'book'],
            ['reading.index', 'History', 'clock'],
        ];
        if ($settings->enable_leaderboard) $links[] = ['leaderboard.index', 'Leaderboard', 'trophy'];
        if ($settings->enable_badges)      $links[] = ['badges.index', 'Achievements', 'medal'];
    }
@endphp

@foreach ($links as [$route, $label, $icon])
    @php
        $active = request()->routeIs($route)
            || ($route === 'reading.create' && request()->routeIs('reading.timer'))
            || ($route === 'quran.index' && request()->routeIs('quran.show'));
    @endphp
    <a href="{{ route($route) }}" class="side-link {{ $active ? 'side-link-active' : '' }}">
        <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" d="{{ $icons[$icon] }}"/>
        </svg>
        <span>{{ $label }}</span>
    </a>
@endforeach
