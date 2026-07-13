@props(['event'])

<a href="#" class="flex items-center gap-4 bg-white/5 hover:bg-white/10 rounded-lg p-4 transition-colors group">
    {{-- Date badge --}}
    <div class="flex flex-col items-center justify-center bg-secondary-fixed text-on-secondary-fixed rounded-lg w-14 h-14 flex-shrink-0">
        <span class="text-[10px] font-label-md uppercase">{{ $event->event_date->format('M') }}</span>
        <span class="text-xl font-bold leading-none">{{ $event->event_date->format('d') }}</span>
    </div>

    <div class="flex-1 min-w-0">
        <h4 class="font-body-lg text-body-lg text-white font-semibold truncate">{{ $event->title }}</h4>
        <div class="flex flex-wrap gap-x-3 gap-y-1 text-white/60 text-sm mt-1">
            @if($event->event_time)
                <span class="flex items-center gap-1">
                    <x-icon name="schedule" class="text-base" /> {{ $event->event_time->format('g:i A') }}
                </span>
            @endif
            @if($event->location)
                <span class="flex items-center gap-1">
                    <x-icon name="location_on" class="text-base" /> {{ $event->location }}
                </span>
            @endif
        </div>
    </div>

    <x-icon name="chevron_right" class="text-white/40 group-hover:text-secondary-fixed group-hover:translate-x-1 transition-all flex-shrink-0" />
</a>