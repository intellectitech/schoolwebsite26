@props(['label', 'count', 'icon', 'route'])

<a
    href="{{ route($route) }}"
    class="bg-white rounded-xl border border-outline-variant/20 p-5 flex items-center gap-4
           hover:shadow-[0_4px_16px_-4px_rgba(0,30,64,0.1)] hover:-translate-y-0.5 transition-all"
>
    <div class="w-11 h-11 rounded-lg bg-primary/5 flex items-center justify-center text-primary flex-shrink-0">
        <x-icon :name="$icon" />
    </div>
    <div class="min-w-0">
        <p class="text-2xl font-bold text-on-surface leading-none">{{ $count }}</p>
        <p class="font-caption text-caption text-on-surface-variant mt-1 truncate">{{ $label }}</p>
    </div>
</a>