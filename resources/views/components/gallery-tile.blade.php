@props(['item'])

<a href="#" class="relative rounded-xl overflow-hidden group aspect-square block">
    <img
        src="{{ $item->image_url }}"
        alt="{{ $item->title }}"
        class="absolute inset-0 w-full h-full object-cover group-hover:scale-105 transition-transform duration-500"
    >
    <div class="absolute inset-0 bg-gradient-to-t from-black/70 via-black/10 to-transparent"></div>
    <div class="absolute bottom-0 left-0 right-0 p-4">
        <h3 class="font-headline-md text-headline-md !text-lg text-white">{{ $item->title }}</h3>
        <p class="text-white/70 text-xs mt-0.5">{{ $item->tagline }}</p>
    </div>
</a>