@props([
    'label' => '',
    'value' => 0,
    'suffix' => '',
    'icon' => '📖',
    'accent' => 'brand',
    'animate' => true,
])

@php
    $accents = [
        'brand' => 'from-brand-500/10 to-brand-500/0 text-brand-600',
        'gold'  => 'from-gold-500/10 to-gold-500/0 text-gold-600',
        'blue'  => 'from-sky-500/10 to-sky-500/0 text-sky-600',
        'rose'  => 'from-rose-500/10 to-rose-500/0 text-rose-600',
        'violet'=> 'from-violet-500/10 to-violet-500/0 text-violet-600',
    ];
    $cls = $accents[$accent] ?? $accents['brand'];
@endphp

<div {{ $attributes->merge(['class' => 'stat-card']) }}>
    <div class="absolute inset-0 bg-gradient-to-br {{ explode(' ', $cls)[0] }} {{ explode(' ', $cls)[1] }} pointer-events-none"></div>
    <div class="relative flex items-start justify-between">
        <div>
            <p class="text-xs font-medium text-slate-500 dark:text-slate-400 uppercase tracking-wide">{{ $label }}</p>
            @if ($animate && is_numeric($value))
                <p class="mt-1.5 text-3xl font-extrabold" x-data="counter({{ (int) $value }})" x-text="value"></p>
            @else
                <p class="mt-1.5 text-3xl font-extrabold">{{ $value }}</p>
            @endif
            @if ($suffix)
                <p class="text-xs text-slate-400 mt-0.5">{{ $suffix }}</p>
            @endif
        </div>
        <div class="w-11 h-11 rounded-2xl bg-white/70 dark:bg-slate-900/40 grid place-items-center text-xl shadow-sm shrink-0">
            {{ $icon }}
        </div>
    </div>
</div>
