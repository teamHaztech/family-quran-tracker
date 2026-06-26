<x-app-layout>
    <x-slot name="title">Log Reading</x-slot>

    <div class="max-w-2xl mx-auto space-y-6">

        {{-- Method switcher --}}
        <div class="grid grid-cols-2 gap-3">
            <div class="card p-4 border-2 !border-brand-500 flex items-center gap-3">
                <span class="w-10 h-10 rounded-xl bg-brand-100 dark:bg-brand-900/40 grid place-items-center text-xl">✍️</span>
                <div>
                    <p class="font-bold text-sm">Manual Entry</p>
                    <p class="text-xs text-slate-400">Log a past session</p>
                </div>
            </div>
            <a href="{{ route('reading.timer') }}" class="card card-hover p-4 flex items-center gap-3">
                <span class="w-10 h-10 rounded-xl bg-slate-100 dark:bg-slate-700 grid place-items-center text-xl">⏱️</span>
                <div>
                    <p class="font-bold text-sm">Reading Timer</p>
                    <p class="text-xs text-slate-400">Read &amp; track live</p>
                </div>
            </a>
        </div>

        <div class="card p-6 sm:p-8">
            <h2 class="text-xl font-extrabold mb-1">Manual Reading Entry</h2>
            <p class="text-sm text-slate-500 dark:text-slate-400 mb-6">Record what you read today (or on a past day).</p>

            <form method="POST" action="{{ route('reading.store') }}" class="space-y-5">
                @csrf
                <input type="hidden" name="method" value="manual">
                @include('reading._fields')

                <div class="flex justify-end gap-3 pt-2">
                    <a href="{{ route('member.dashboard') }}" class="btn-secondary">Cancel</a>
                    <button type="submit" class="btn-primary">Save Session</button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
