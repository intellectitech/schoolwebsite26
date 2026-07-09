@props(['pathway'])

<div class="bg-white rounded-xl border border-outline-variant/20 overflow-hidden flex flex-col group">
    <div class="h-40 overflow-hidden">
        <img
            src="{{ $pathway->image_url }}"
            alt="{{ $pathway->title }}"
            class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500"
        >
    </div>
    <div class="p-5 flex flex-col flex-1">
        <h3 class="font-headline-md text-headline-md !text-lg text-on-surface mb-2">{{ $pathway->title }}</h3>
        <p class="font-body-md text-sm text-on-surface-variant flex-1">{{ $pathway->description }}</p>

        <a
            href="{{ $pathway->link_url ?? '#' }}"
            class="inline-flex items-center gap-1 font-label-md text-label-md text-primary mt-4 hover:gap-2 transition-all"
        >
            Learn More <x-icon name="arrow_forward" class="text-base" />
        </a>
    </div>
</div>