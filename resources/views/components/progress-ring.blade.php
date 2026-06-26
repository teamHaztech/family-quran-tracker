@props([
    'percent' => 0,
    'size' => 120,
    'stroke' => 10,
    'label' => '',
    'center' => null,
])

@php
    $percent = max(0, min(100, (int) $percent));
    $radius = ($size - $stroke) / 2;
    $circ = 2 * M_PI * $radius;
    $offset = $circ * (1 - $percent / 100);
@endphp

<div class="inline-flex flex-col items-center gap-1.5" {{ $attributes }}>
    <div class="relative" style="width: {{ $size }}px; height: {{ $size }}px;">
        <svg class="-rotate-90" width="{{ $size }}" height="{{ $size }}">
            <circle cx="{{ $size/2 }}" cy="{{ $size/2 }}" r="{{ $radius }}" fill="none"
                    stroke="currentColor" class="text-slate-100 dark:text-slate-700" stroke-width="{{ $stroke }}"/>
            <circle cx="{{ $size/2 }}" cy="{{ $size/2 }}" r="{{ $radius }}" fill="none"
                    stroke="currentColor" class="text-brand-500" stroke-width="{{ $stroke }}"
                    stroke-linecap="round"
                    stroke-dasharray="{{ $circ }}"
                    style="stroke-dashoffset: {{ $circ }}; transition: stroke-dashoffset 1s ease-out;"
                    x-data x-init="$nextTick(() => $el.style.strokeDashoffset = {{ $offset }})"/>
        </svg>
        <div class="absolute inset-0 grid place-items-center text-center">
            @if ($center)
                {{ $center }}
            @else
                <div>
                    <span class="text-2xl font-extrabold" x-data="counter({{ $percent }})" x-text="value + '%'"></span>
                </div>
            @endif
        </div>
    </div>
    @if ($label)
        <span class="text-xs font-medium text-slate-500 dark:text-slate-400">{{ $label }}</span>
    @endif
</div>
