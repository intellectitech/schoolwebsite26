@props(['title', 'createRoute' => null, 'createLabel' => 'Add New'])

<div class="flex items-center justify-between mb-6">
    <h2 class="font-headline-md text-headline-md text-on-surface">{{ $title }}</h2>
    @if($createRoute)
        <x-button variant="primary" :href="route($createRoute)" class="!px-5 !py-2.5">
            <x-icon name="add" class="text-base" /> {{ $createLabel }}
        </x-button>
    @endif
</div>