@props(['active' => null])

@php
    $items = [
        'home' => ['label' => 'Home', 'icon' => 'home', 'route' => 'home'],
        'academics' => ['label' => 'Academics', 'icon' => 'school', 'route' => 'academics'],
        'admissions' => ['label' => 'Admissions', 'icon' => 'how_to_reg', 'route' => 'admissions'],
        'campus' => ['label' => 'Campus', 'icon' => 'diversity_3', 'route' => 'campus'],
    ];
@endphp

<nav class="md:hidden fixed bottom-0 left-0 w-full flex justify-around items-center h-16 px-2 bg-surface-container shadow-[0_-4px_6px_-1px_rgba(0,30,64,0.08)] z-50">
    @foreach($items as $key => $item)
        <a
            href="{{ route($item['route']) }}"
            class="flex flex-col items-center justify-center transition-transform active:scale-90 p-2 rounded-full
                {{ $active === $key
                    ? 'bg-secondary-container text-on-secondary-container px-4 py-1'
                    : 'text-on-surface-variant' }}"
        >
            <x-icon :name="$item['icon']" />
            <span class="text-[10px] font-label-md">{{ $item['label'] }}</span>
        </a>
    @endforeach
</nav>