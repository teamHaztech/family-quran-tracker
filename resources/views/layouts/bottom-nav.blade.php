@php
    $bIcons = [
        'home'   => 'M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6',
        'users'  => 'M17 20h5v-2a4 4 0 00-3-3.87M9 20H4v-2a4 4 0 013-3.87m6-1.13a4 4 0 10-4-4 4 4 0 004 4z',
        'book'   => 'M12 6.25C10.5 5 8 4.5 5 4.75v12.5C8 17 10.5 17.5 12 18.75M12 6.25C13.5 5 16 4.5 19 4.75v12.5C16 17 13.5 17.5 12 18.75M12 6.25v12.5',
        'clock'  => 'M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z',
        'trophy' => 'M8 21h8m-4-4v4m5-16h2a2 2 0 010 4h-.5M7 5H5a2 2 0 000 4h.5M7 4h10v5a5 5 0 01-10 0V4z',
        'doc'    => 'M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z',
        'cog'    => 'M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065zM15 12a3 3 0 11-6 0 3 3 0 016 0z',
    ];

    if (auth()->user()->isAdmin()) {
        $items = [
            ['admin.dashboard', 'Home', 'home'],
            ['admin.members.index', 'Members', 'users'],
            ['admin.reports.index', 'Reports', 'doc'],
            ['admin.settings.edit', 'Settings', 'cog'],
        ];
    } else {
        $items = [
            ['member.dashboard', 'Home', 'home'],
            ['reading.index', 'History', 'clock'],
            ['quran.index', 'Read', 'book'],
            ['leaderboard.index', 'Ranks', 'trophy'],
        ];
    }
@endphp

@foreach ($items as $i => [$route, $label, $icon])
    @php $active = request()->routeIs($route); @endphp
    @if ($icon === 'book')
        {{-- Center floating "Read" action --}}
        <a href="{{ route($route) }}" class="flex-1 flex justify-center">
            <span class="-mt-6 w-14 h-14 rounded-full bg-brand-600 text-white grid place-items-center shadow-soft-lg ring-4 ring-slate-50 dark:ring-slate-900">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="{{ $bIcons[$icon] }}"/></svg>
            </span>
        </a>
    @else
        <a href="{{ route($route) }}" class="bottom-link {{ $active ? 'bottom-link-active' : '' }}">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="{{ $bIcons[$icon] }}"/></svg>
            <span>{{ $label }}</span>
        </a>
    @endif
@endforeach
