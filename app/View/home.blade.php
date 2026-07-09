<x-layouts.app :title="'Home'" :active="'home'">

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
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4 md:gap-6 -mt-12 md:-mt-16 relative z-10">
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
    <section class="py-section-padding-lg">
        <div class="max-w-container mx-auto px-6 md:px-12">
            <div class="flex items-end justify-between mb-8">
                <h2 class="font-headline-lg text-headline-lg text-on-surface">Academy News</h2>
                <a href="#" class="font-label-md text-label-md text-secondary hover:underline">View All Stories</a>
            </div>

            @if($featuredPost || $secondaryPosts->isNotEmpty())
                <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                    {{-- Featured story --}}
                    @if($featuredPost)
                        <a href="#" class="lg:col-span-2 relative rounded-xl overflow-hidden group h-80 lg:h-auto">
                            <img
                                src="{{ $featuredPost->image_url }}"
                                alt="{{ $featuredPost->title }}"
                                class="absolute inset-0 w-full h-full object-cover group-hover:scale-105 transition-transform duration-500"
                            >
                            <div class="absolute inset-0 bg-gradient-to-t from-black/80 via-black/20 to-transparent"></div>
                            <div class="absolute bottom-0 left-0 right-0 p-6 md:p-8">
                                @if($featuredPost->category)
                                    <span class="font-label-md text-label-md text-secondary-fixed uppercase">{{ $featuredPost->category }}</span>
                                @endif
                                <h3 class="font-headline-lg text-headline-lg !text-2xl md:!text-3xl text-white mt-2 leading-snug">
                                    {{ $featuredPost->title }}
                                </h3>
                                <p class="text-white/75 text-sm mt-2 max-w-lg line-clamp-2">{{ $featuredPost->excerpt }}</p>
                            </div>
                        </a>
                    @endif

                    {{-- Secondary stories --}}
                    <div class="flex flex-col gap-6">
                        @foreach($secondaryPosts as $post)
                            <a href="#" class="flex gap-4 group">
                                <img
                                    src="{{ $post->image_url }}"
                                    alt="{{ $post->title }}"
                                    class="w-24 h-24 rounded-lg object-cover flex-shrink-0"
                                >
                                <div>
                                    <h4 class="font-body-lg text-body-lg font-semibold text-on-surface group-hover:text-primary transition-colors leading-snug">
                                        {{ $post->title }}
                                    </h4>
                                    <p class="text-on-surface-variant text-sm mt-1 line-clamp-2">{{ $post->excerpt }}</p>
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

            <form action="#" method="POST" class="flex flex-col sm:flex-row gap-3">
                @csrf
                <input
                    type="email"
                    name="email"
                    placeholder="Email Address"
                    required
                    class="flex-1 rounded-lg border border-outline-variant px-4 py-3.5 font-body-md text-sm
                           focus:outline-none focus:ring-2 focus:ring-primary focus:border-primary"
                >
                <x-button type="submit" variant="primary">Subscribe</x-button>
            </form>
        </div>
    </section>

</x-layouts.app>