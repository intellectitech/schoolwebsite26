<x-layouts.admin :title="'Dashboard'" :active="'dashboard'">

    {{-- ============ STAT CARDS ============ --}}
    <div class="grid grid-cols-2 md:grid-cols-3 xl:grid-cols-4 gap-4 mb-8">
        @foreach($stats as $stat)
            <x-admin.stat-card
                :label="$stat['label']"
                :count="$stat['count']"
                :icon="$stat['icon']"
                :route="$stat['route']"
            />
        @endforeach
    </div>

    {{-- ============ RECENT ACTIVITY ============ --}}
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

        <x-admin.panel-card title="Recent News Posts">
            @forelse($recentNews as $post)
                @php
                    $postImage = $post->image_url;
                    if (str_contains(strtolower($post->title), 'quantum')) {
                        $postImage = asset('images/quantum.jpg');
                    }
                @endphp
                <div class="flex items-center gap-3 px-5 py-3">
                    <img
                        src="{{ $postImage }}"
                        alt="{{ $post->title }}"
                        class="w-12 h-12 rounded-lg object-cover flex-shrink-0"
                    >
                    <div class="min-w-0 flex-1">
                        <p class="font-body-md text-sm font-semibold text-on-surface truncate">{{ $post->title }}</p>
                        <p class="text-xs text-on-surface-variant">
                            {{ $post->published_at?->format('M j, Y') ?? 'Not published' }}
                            @if($post->is_featured)
                                <span class="ml-2 inline-block bg-secondary-fixed/20 text-secondary text-[10px] px-2 py-0.5 rounded-full">Featured</span>
                            @endif
                        </p>
                    </div>
                </div>
            @empty
                <p class="px-5 py-6 text-sm text-on-surface-variant text-center">No news posts yet.</p>
            @endforelse
        </x-admin.panel-card>

        <x-admin.panel-card title="Upcoming Events">
            @forelse($upcomingEvents as $event)
                <div class="flex items-center gap-3 px-5 py-3">
                    <div class="flex flex-col items-center justify-center bg-primary/5 text-primary rounded-lg w-12 h-12 flex-shrink-0">
                        <span class="text-[10px] font-label-md uppercase">{{ $event->event_date->format('M') }}</span>
                        <span class="text-base font-bold leading-none">{{ $event->event_date->format('d') }}</span>
                    </div>
                    <div class="min-w-0 flex-1">
                        <p class="font-body-md text-sm font-semibold text-on-surface truncate">{{ $event->title }}</p>
                        <p class="text-xs text-on-surface-variant">{{ $event->location ?? 'Location TBD' }}</p>
                    </div>
                </div>
            @empty
                <p class="px-5 py-6 text-sm text-on-surface-variant text-center">No upcoming events.</p>
            @endforelse
        </x-admin.panel-card>

    </div>

</x-layouts.admin>