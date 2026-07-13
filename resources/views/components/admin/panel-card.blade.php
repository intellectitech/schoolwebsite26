@props(['title', 'viewAllRoute' => null])

<div class="bg-white rounded-xl border border-outline-variant/20 overflow-hidden">
    <div class="flex items-center justify-between px-5 py-4 border-b border-outline-variant/20">
        <h3 class="font-body-lg text-body-lg !text-base font-semibold text-on-surface">{{ $title }}</h3>
        @if($viewAllRoute)
            <a href="{{ route($viewAllRoute) }}" class="font-label-md text-label-md text-primary hover:underline">
                View All
            </a>
        @endif
    </div>
    <div class="divide-y divide-outline-variant/10">
        {{ $slot }}
    </div>
</div>