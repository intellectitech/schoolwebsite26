<x-app-layout :title="'Home'" :active="'home'">

    {{-- ============ HERO ============ --}}
    <section class="relative">
        <div class="relative h-[500px] md:h-[600px] overflow-hidden">
            <img
                src="https://images.unsplash.com/photo-1607237138185-eedd9c632b0b?q=80&w=2000"
                alt="Starlight Academy campus buildings at dusk"
                class="absolute inset-0 w-full h-full object-cover"
            >
            <div class="absolute inset-0 hero-gradient"></div>

            <div class="relative h-full max-w-container mx-auto px-6 md:px-12 flex flex-col justify-center">
                <span class="inline-block w-fit bg-secondary-fixed text-on-secondary-fixed font-label-md text-label-md px-3 py-1 rounded-full mb-4">
                    Est. 1894
                </span>

                <h1 class="font-display-lg-mobile md:font-display-lg text-display-lg-mobile md:text-display-lg text-white max-w-2xl">
                    Cultivating Wisdom, Shaping the Future.
                </h1>

                <p class="font-body-lg text-body-lg text-white/85 max-w-xl mt-4">
                    Join a global community of scholars dedicated to rigorous inquiry, creative expression, and social responsibility within a tradition of excellence.
                </p>

                <div class="flex flex-wrap gap-4 mt-8">
                    <x-button variant="gold" href="{{ route('academics') }}">Explore Curriculum</x-button>
                    <x-button variant="outline-white" href="#">Virtual Tour</x-button>
                </div>
            </div>
        </div>

        {{-- Quick-link cards overlapping the hero bottom edge --}}
        <div class="max-w-container mx-auto px-6 md:px-12">
            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-4 md:gap-6 -mt-8 sm:-mt-12 md:-mt-16 relative z-10">
                <x-quick-link-card
                    icon="school"
                    title="Academics"
                    description="Over 120 undergraduate and graduate programs."
                    :href="route('academics')"
                />
                <x-quick-link-card
                    icon="groups"
                    title="Faculty"
                    description="Learn from world-renowned researchers and artists."
                    :href="route('campus')"
                />
                <x-quick-link-card
                    icon="account_balance"
                    title="Admissions"
                    description="Start your journey at Starlight Academy today."
                    :href="route('admissions')"
                />
                <x-quick-link-card
                    icon="diversity_3"
                    title="Student Life"
                    description="Discover a vibrant campus with over 200 clubs."
                    :href="route('campus')"
                />
            </div>
        </div>
    </section>

    {{-- ============ ACADEMY NEWS ============ --}}
    <section class="py-section-padding-lg bg-[linear-gradient(135deg,rgba(255,255,255,0.98),rgba(248,244,232,0.96))] rounded-[2rem] shadow-[0_18px_60px_-24px_rgba(15,23,42,0.35)] border border-outline-variant/20">
        <div class="max-w-container mx-auto px-6 md:px-12 py-10">
            <div class="flex flex-col gap-4 md:flex-row md:items-end md:justify-between mb-8 sm:mb-10">
                <div class="max-w-2xl text-center md:text-left">
                    <div class="inline-flex items-center gap-3 mb-4">
                        <span class="h-2.5 w-2.5 rounded-full bg-secondary-fixed shadow-[0_0_0_8px_rgba(245,158,11,0.12)]"></span>
                        <span class="font-label-md text-label-md uppercase tracking-[0.3em] text-secondary-fixed">Campus Stories</span>
                    </div>
                    <h2 class="flex items-center gap-3 font-headline-lg text-headline-lg text-on-surface">
                        <span class="inline-flex h-8 w-8 items-center justify-center rounded-full border border-secondary-fixed/30 bg-secondary-fixed/10 text-secondary-fixed text-lg shadow-sm">✦</span>
                        <span>Academy News</span>
                    </h2>
                    <p class="mt-3 max-w-xl text-body-md text-on-surface-variant">
                        Current headlines, campus stories, and research highlights from Starlight Academy, arranged for fast reading and easy discovery.
                    </p>
                </div>
                <a href="#" class="inline-flex items-center justify-center md:justify-start gap-2 font-label-md text-label-md text-secondary hover:text-secondary-fixed transition-colors">
                    View All Stories
                    <x-icon name="arrow_forward" class="text-base" />
                </a>
            </div>

            @if($featuredPost || $secondaryPosts->isNotEmpty())
                <div class="grid grid-cols-1 lg:grid-cols-[minmax(0,2fr)_1fr] gap-6">
                    {{-- Featured story --}}
                    @if($featuredPost)
                        @php
                            $featuredImage = $featuredPost->image_url;
                            if (str_contains(strtolower($featuredPost->title), 'quantum')) {
                                $featuredImage = asset('images/quantum.jpg');
                            }
                        @endphp
                        <a href="#" class="group relative overflow-hidden rounded-[1.75rem] border border-outline-variant/20 shadow-[0_16px_40px_-28px_rgba(0,0,0,0.35)] min-h-[420px] bg-surface">
                            <div class="absolute inset-0 overflow-hidden">
                                <img
                                    src="{{ $featuredImage }}"
                                    alt="{{ $featuredPost->title }}"
                                    class="absolute inset-0 h-full w-full object-cover transition-transform duration-500 group-hover:scale-105"
                                >
                                <div class="absolute inset-0 bg-gradient-to-t from-black/90 via-black/20 to-transparent"></div>
                            </div>
                            <div class="relative p-6 md:p-8 flex h-full flex-col justify-end">
                                @if($featuredPost->category)
                                    <span class="font-label-md text-label-md text-secondary-fixed uppercase">{{ $featuredPost->category }}</span>
                                @endif
                                <h3 class="font-headline-lg text-headline-lg md:text-3xl text-white mt-4 leading-snug">
                                    {{ $featuredPost->title }}
                                </h3>
                                <p class="text-white/80 text-sm mt-4 max-w-3xl line-clamp-3">{{ $featuredPost->excerpt }}</p>
                                <span class="mt-6 inline-flex items-center gap-2 font-label-md text-label-md text-white/80">
                                    Read story
                                    <x-icon name="arrow_forward" class="text-base" />
                                </span>
                            </div>
                        </a>
                    @endif

                    {{-- Secondary stories --}}
                    <div class="grid grid-cols-1 gap-6">
                        @foreach($secondaryPosts as $post)
                            <a href="#" class="group grid grid-cols-1 sm:grid-cols-[90px_1fr] items-start gap-4 rounded-[1.5rem] border border-outline-variant/20 bg-surface p-5 transition-transform duration-300 hover:-translate-y-1 shadow-sm">
                                <img
                                    src="{{ $post->image_url }}"
                                    alt="{{ $post->title }}"
                                    class="h-24 w-full rounded-2xl object-cover sm:h-24 sm:w-24"
                                >
                                <div class="flex flex-col justify-between">
                                    <div>
                                        @if($post->category)
                                            <span class="font-label-sm text-label-sm text-secondary-fixed uppercase">{{ $post->category }}</span>
                                        @endif
                                        <h4 class="mt-3 font-body-lg text-body-lg font-semibold text-on-surface group-hover:text-primary transition-colors leading-snug">
                                            {{ $post->title }}
                                        </h4>
                                        <p class="text-on-surface-variant text-sm mt-2 line-clamp-3">{{ $post->excerpt }}</p>
                                    </div>
                                    <span class="mt-4 inline-flex items-center gap-2 font-label-sm text-label-sm text-secondary-fixed">
                                        Read more
                                        <x-icon name="arrow_forward" class="text-xs" />
                                    </span>
                                </div>
                            </a>
                        @endforeach
                    </div>
                </div>
            @else
                <p class="text-on-surface-variant">No news posts published yet.</p>
            @endif
        </div>
    </section>

    {{-- ============ UPCOMING EVENTS ============ --}}
    <section class="bg-primary py-section-padding-lg">
        <div class="max-w-container mx-auto px-6 md:px-12 grid grid-cols-1 lg:grid-cols-2 gap-10 items-start">
            <div>
                <h2 class="font-headline-lg text-headline-lg text-white mb-4">Upcoming Events</h2>
                <p class="font-headline-md text-headline-md italic text-white/70 border-l-2 border-secondary-fixed pl-4 mb-8">
                    "Education is not the filling of a pail, but the lighting of a fire."
                </p>
                <x-button variant="outline-white" href="#">
                    <x-icon name="calendar_month" class="text-base" /> View Campus Calendar
                </x-button>
            </div>

            <div class="flex flex-col gap-3">
                @forelse($upcomingEvents as $event)
                    <x-event-row :event="$event" />
                @empty
                    <p class="text-white/60">No upcoming events scheduled.</p>
                @endforelse
            </div>
        </div>
    </section>

    {{-- ============ STAY CONNECTED ============ --}}
    <section class="py-section-padding-lg bg-surface-bright">
        <div class="max-w-[600px] mx-auto px-6 text-center">
            <h2 class="font-headline-lg text-headline-lg text-on-surface mb-3">Stay Connected</h2>
            <p class="font-body-md text-on-surface-variant mb-8">
                Receive monthly insights, research updates, and campus stories directly in your inbox.
            </p>

            <form action="{{ route('subscribe') }}" method="POST" class="flex flex-col sm:flex-row gap-3">
                @csrf
                <input
                    type="email"
                    name="email"
                    placeholder="Email Address"
                    required
                    autocomplete="email"
                    class="flex-1 rounded-lg border border-outline-variant px-4 py-3.5 font-body-md text-sm
                           focus:outline-none focus:ring-2 focus:ring-primary focus:border-primary"
                >
                <x-button type="submit" variant="primary">Subscribe</x-button>
            </form>
        </div>
    </section>

</x-app-layout>