@php
    $user = auth()->user();
    $isAdmin = $user?->isAdmin();
    $settings = \App\Models\Setting::current();
@endphp
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}"
      x-data="{ dark: localStorage.getItem('theme') === 'dark' }"
      x-init="$watch('dark', v => localStorage.setItem('theme', v ? 'dark' : 'light'))"
      :class="{ 'dark': dark }">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="theme-color" content="#047857">
    <title>{{ $settings->family_name }} · {{ config('app.name') }}</title>

    {{-- Prevent dark-mode flash before Alpine boots --}}
    <script>
        if (localStorage.getItem('theme') === 'dark') document.documentElement.classList.add('dark');
    </script>

    <link rel="manifest" href="{{ asset('manifest.json') }}">
    <link rel="apple-touch-icon" href="{{ asset('icons/icon-192.png') }}">
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700,800|amiri:400,700&display=swap" rel="stylesheet">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>[x-cloak]{display:none!important}</style>
</head>
<body class="font-sans text-slate-800 dark:text-slate-100 bg-slate-50 dark:bg-slate-900 min-h-screen">

<div class="flex min-h-screen">

    {{-- ============================ SIDEBAR (desktop) ============================ --}}
    <aside class="hidden lg:flex lg:flex-col w-64 shrink-0 bg-white dark:bg-slate-800/60 border-r border-slate-100 dark:border-slate-700/60 fixed inset-y-0 z-30">
        <div class="h-16 flex items-center gap-2.5 px-5 border-b border-slate-100 dark:border-slate-700/60">
            <div class="w-9 h-9 rounded-xl bg-brand-600 text-white grid place-items-center text-lg shadow-soft">☪</div>
            <div class="leading-tight">
                <p class="font-extrabold text-brand-700 dark:text-brand-400">Quran Tracker</p>
                <p class="text-[11px] text-slate-400 truncate max-w-[150px]">{{ $settings->family_name }}</p>
            </div>
        </div>

        <nav class="flex-1 overflow-y-auto px-3 py-4 space-y-1">
            @include('layouts.nav-links')
        </nav>

        <div class="p-3 border-t border-slate-100 dark:border-slate-700/60">
            <a href="{{ route('profile.edit') }}" class="flex items-center gap-3 rounded-xl p-2 hover:bg-slate-100 dark:hover:bg-slate-700/60 transition">
                <img src="{{ $user->photoUrl() }}" class="w-9 h-9 rounded-full object-cover" alt="">
                <div class="min-w-0">
                    <p class="text-sm font-semibold truncate">{{ $user->fullName() }}</p>
                    <p class="text-[11px] text-slate-400">{{ $isAdmin ? 'Family Leader' : 'Member' }}</p>
                </div>
            </a>
        </div>
    </aside>

    {{-- ============================ MAIN ============================ --}}
    <div class="flex-1 lg:ml-64 flex flex-col min-w-0">

        {{-- Sticky header --}}
        <header class="sticky top-0 z-20 h-16 bg-white/80 dark:bg-slate-900/80 backdrop-blur border-b border-slate-100 dark:border-slate-700/60">
            <div class="h-full px-4 sm:px-6 flex items-center justify-between gap-3">
                <div class="flex items-center gap-2 min-w-0">
                    <span class="lg:hidden w-8 h-8 rounded-lg bg-brand-600 text-white grid place-items-center shrink-0">☪</span>
                    <h1 class="text-lg font-bold truncate">{{ $title ?? 'Dashboard' }}</h1>
                </div>

                <div class="flex items-center gap-2">
                    {{-- Dark mode toggle --}}
                    <button @click="dark = !dark" class="w-10 h-10 rounded-xl grid place-items-center hover:bg-slate-100 dark:hover:bg-slate-700/60 transition" title="Toggle theme">
                        <svg x-show="!dark" class="w-5 h-5 text-slate-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M21 12.79A9 9 0 1111.21 3 7 7 0 0021 12.79z"/></svg>
                        <svg x-show="dark" x-cloak class="w-5 h-5 text-gold-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.36 6.36l-.7-.7M6.34 6.34l-.7-.7m12.72 0l-.7.7M6.34 17.66l-.7.7M16 12a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
                    </button>

                    {{-- Quick "Read" CTA for members --}}
                    @unless($isAdmin)
                        <a href="{{ route('reading.timer') }}" class="btn-primary hidden sm:inline-flex !py-2 !px-3.5">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6.25C10.5 5 8 4.5 5 4.75v12.5C8 17 10.5 17.5 12 18.75M12 6.25C13.5 5 16 4.5 19 4.75v12.5C16 17 13.5 17.5 12 18.75M12 6.25v12.5"/></svg>
                            Read
                        </a>
                    @endunless

                    {{-- User dropdown --}}
                    <div x-data="{ open: false }" class="relative">
                        <button @click="open = !open" class="flex items-center gap-2 rounded-xl p-1 pr-2 hover:bg-slate-100 dark:hover:bg-slate-700/60 transition">
                            <img src="{{ $user->photoUrl() }}" class="w-8 h-8 rounded-full object-cover" alt="">
                            <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/></svg>
                        </button>
                        <div x-show="open" x-cloak @click.outside="open = false" x-transition
                             class="absolute right-0 mt-2 w-52 card p-2 z-50">
                            <p class="px-3 py-2 text-xs text-slate-400 truncate">{{ $user->email }}</p>
                            <a href="{{ route('profile.edit') }}" class="block px-3 py-2 rounded-lg text-sm hover:bg-slate-100 dark:hover:bg-slate-700/60">Profile &amp; Password</a>
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button class="w-full text-left px-3 py-2 rounded-lg text-sm text-red-600 hover:bg-red-50 dark:hover:bg-red-900/30">Log Out</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </header>

        {{-- Flash messages --}}
        <div class="px-4 sm:px-6 pt-4 space-y-2">
            @if (session('success'))
                <div x-data="{ show: true }" x-show="show" x-transition class="card border-l-4 !border-l-brand-500 px-4 py-3 flex items-start gap-2">
                    <span class="text-brand-600">✓</span>
                    <p class="text-sm flex-1">{{ session('success') }}</p>
                    <button @click="show = false" class="text-slate-400 hover:text-slate-600">&times;</button>
                </div>
            @endif
            @if (session('error'))
                <div class="card border-l-4 !border-l-red-500 px-4 py-3 text-sm text-red-700 dark:text-red-300">{{ session('error') }}</div>
            @endif
        </div>

        {{-- Page content --}}
        <main class="flex-1 px-4 sm:px-6 py-6 pb-24 lg:pb-8 animate-fade-in">
            {{ $slot }}
        </main>

        <footer class="hidden lg:block px-6 py-4 text-center text-xs text-slate-400">
            {{ $settings->family_name }} · Family Quran Tracker v{{ config('quran.version') }}
        </footer>
    </div>
</div>

{{-- ============================ MOBILE BOTTOM NAV ============================ --}}
<nav class="lg:hidden fixed bottom-0 inset-x-0 z-30 bg-white/90 dark:bg-slate-800/90 backdrop-blur border-t border-slate-100 dark:border-slate-700/60 flex pb-[env(safe-area-inset-bottom)]">
    @include('layouts.bottom-nav')
</nav>

{{-- ============================ BADGE CONGRATS POPUP ============================ --}}
@if (session('new_badges'))
    <div x-data="{ show: true }" x-show="show" x-cloak
         class="fixed inset-0 z-50 grid place-items-center bg-black/40 backdrop-blur-sm p-4"
         @keydown.escape.window="show = false">
        <div class="card p-8 max-w-sm w-full text-center animate-pop">
            <div class="text-6xl mb-3">🎉</div>
            <h3 class="text-xl font-extrabold mb-1">Mubarak! New Achievement</h3>
            <p class="text-sm text-slate-500 mb-5">You've earned {{ count(session('new_badges')) }} new badge(s)!</p>
            <div class="flex flex-wrap justify-center gap-3 mb-6">
                @foreach (session('new_badges') as $b)
                    <div class="flex flex-col items-center gap-1">
                        <div class="text-4xl">{{ $b['icon'] }}</div>
                        <span class="text-xs font-semibold">{{ $b['name'] }}</span>
                    </div>
                @endforeach
            </div>
            <button @click="show = false" class="btn-primary w-full">Alhamdulillah 🤲</button>
        </div>
    </div>
@endif

@stack('scripts')
</body>
</html>
