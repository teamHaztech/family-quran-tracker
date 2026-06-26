@props([
    'icon' => '🌙',
    'title' => 'Nothing here yet',
    'message' => '',
])

<div {{ $attributes->merge(['class' => 'flex flex-col items-center justify-center text-center py-14 px-6']) }}>
    <div class="w-20 h-20 rounded-3xl bg-brand-50 dark:bg-slate-700/50 grid place-items-center text-4xl mb-4 animate-pop">
        {{ $icon }}
    </div>
    <h3 class="font-bold text-lg">{{ $title }}</h3>
    @if ($message)
        <p class="text-sm text-slate-500 dark:text-slate-400 mt-1 max-w-sm">{{ $message }}</p>
    @endif
    @if (isset($action))
        <div class="mt-5">{{ $action }}</div>
    @endif
</div>
