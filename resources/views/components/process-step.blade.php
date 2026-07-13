@props(['step', 'isLast' => false])

<div class="relative flex flex-col items-center text-center flex-1">
    {{-- Connecting line --}}
    @unless($isLast)
        <div class="hidden md:block absolute top-6 left-1/2 w-full h-px bg-outline-variant z-0"></div>
    @endunless

    <div
        @class([
            'relative z-10 w-12 h-12 rounded-full flex items-center justify-center mb-4',
            'bg-primary text-white' => $step->step_number == 4,
            'bg-surface-container-high text-primary' => $step->step_number != 4,
        ])
    >
        <x-icon :name="$step->icon" />
    </div>

    <p class="font-label-md text-label-md text-secondary mb-1">
        {{ str_pad($step->step_number, 2, '0', STR_PAD_LEFT) }}. {{ $step->title }}
    </p>
    <p class="font-body-md text-sm text-on-surface-variant max-w-[200px]">{{ $step->description }}</p>
</div>
