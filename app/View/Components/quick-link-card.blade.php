@props(['icon', 'title', 'description', 'href'])


    href="{{ $href }}"
    class="bg-white rounded-xl border border-outline-variant/20 p-6 shadow-[0_4px_16px_-4px_rgba(0,30,64,0.08)]
           hover:shadow-[0_8px_24px_-4px_rgba(0,30,64,0.15)] hover:-translate-y-1 transition-all duration-300 flex flex-col gap-3"
>
    <div class="w-10 h-10 rounded-lg bg-primary/5 flex items-center justify-center text-primary">
        <x-icon :name="$icon" />
    </div>
    <h3 class="font-headline-md text-headline-md !text-lg text-on-surface">{{ $title }}</h3>
    <p class="font-body-md text-sm text-on-surface-variant">{{ $description }}</p>
</a>