<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="dark:bg-slate-900">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="theme-color" content="#047857">
    <title>Sign in · {{ config('app.name') }}</title>

    <script>if (localStorage.getItem('theme') === 'dark') document.documentElement.classList.add('dark');</script>

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700,800&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="font-sans antialiased">
    <div class="min-h-screen grid lg:grid-cols-2">

        {{-- Brand / illustration panel --}}
        <div class="hidden lg:flex flex-col justify-between p-12 bg-gradient-to-br from-brand-700 via-brand-600 to-brand-800 text-white relative overflow-hidden">
            <div class="absolute -top-24 -right-24 w-96 h-96 rounded-full bg-white/5"></div>
            <div class="absolute -bottom-32 -left-16 w-96 h-96 rounded-full bg-white/5"></div>

            <div class="relative flex items-center gap-3">
                <div class="w-11 h-11 rounded-2xl bg-white/15 grid place-items-center text-2xl backdrop-blur">☪</div>
                <span class="text-lg font-extrabold tracking-tight">Family Quran Tracker</span>
            </div>

            <div class="relative">
                <p class="text-3xl font-extrabold leading-snug max-w-md">Read together.<br>Grow together.<br>Track every page as a family.</p>
                <p class="mt-4 text-brand-100/90 max-w-sm text-sm leading-relaxed">
                    "The best of you are those who learn the Qur'an and teach it." — keep your family connected to the Book of Allah, one page at a time.
                </p>
            </div>

            <div class="relative flex items-center gap-6 text-sm text-brand-100">
                <div><span class="block text-2xl font-extrabold text-white">604</span>pages</div>
                <div><span class="block text-2xl font-extrabold text-white">30</span>juz</div>
                <div><span class="block text-2xl font-extrabold text-white">114</span>surahs</div>
            </div>
        </div>

        {{-- Form panel --}}
        <div class="flex flex-col justify-center items-center p-6 sm:p-12 bg-slate-50 dark:bg-slate-900">
            <div class="w-full max-w-md">
                <div class="flex lg:hidden items-center gap-2.5 justify-center mb-8">
                    <div class="w-10 h-10 rounded-2xl bg-brand-600 text-white grid place-items-center text-xl">☪</div>
                    <span class="font-extrabold text-brand-700 dark:text-brand-400 text-lg">Quran Tracker</span>
                </div>

                <div class="card p-8">
                    {{ $slot }}
                </div>

                <p class="text-center text-xs text-slate-400 mt-6">
                    Family Quran Tracker · v{{ config('quran.version') }}
                </p>
            </div>
        </div>
    </div>
</body>
</html>
