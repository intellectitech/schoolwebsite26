@props([
    'variant' => 'primary', // primary | gold | outline | ghost
    'href' => null,
    'type' => 'button',
])

@php
    $base = 'inline-flex items-center justify-center gap-2 font-label-md text-label-md px-8 py-3.5 rounded-lg transition-all active:scale-95 whitespace-nowrap';

    $variants = [
        'primary' => 'bg-primary text-on-primary hover:bg-primary-container shadow-md',
        'gold'    => 'bg-secondary-fixed text-on-secondary-fixed hover:bg-secondary-container shadow-md',
        'outline' => 'border-2 border-primary text-primary hover:bg-primary/5',
        'outline-white' => 'border-2 border-white/40 text-white hover:bg-white/10',
        'ghost'   => 'text-primary hover:bg-primary/5',
    ];

    $classes = $base . ' ' . ($variants[$variant] ?? $variants['primary']);
@endphp

@if($href)
    <a href="{{ $href }}" {{ $attributes->merge(['class' => $classes]) }}>
        {{ $slot }}
    </a>
@else
    <button type="{{ $type }}" {{ $attributes->merge(['class' => $classes]) }}>
        {{ $slot }}
    </button>
@endif