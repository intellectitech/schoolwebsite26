@props(['member'])

<div class="flex flex-col">
    <div class="aspect-[4/5] rounded-lg overflow-hidden mb-4 grayscale hover:grayscale-0 transition-all duration-500">
        <img src="{{ $member->photo_url }}" alt="{{ $member->name }}" class="w-full h-full object-cover">
    </div>
    <h3 class="font-headline-md text-headline-md !text-lg text-on-surface">{{ $member->name }}</h3>
    <p class="font-label-md text-label-md text-secondary mt-1">{{ $member->title }}</p>
    @if($member->bio)
        <p class="font-body-md text-sm text-on-surface-variant mt-2">{{ $member->bio }}</p>
    @endif
</div>