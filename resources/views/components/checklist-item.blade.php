@props(['title', 'description'])

<div class="flex gap-3">
    <div class="w-6 h-6 rounded-full bg-secondary-fixed/20 flex items-center justify-center flex-shrink-0 mt-0.5">
        <x-icon name="check" class="text-secondary-fixed !text-base" />
    </div>
    <div>
        <h4 class="font-body-lg text-body-lg font-semibold text-white">{{ $title }}</h4>
        <p class="text-white/60 text-sm mt-0.5">{{ $description }}</p>
    </div>
</div>