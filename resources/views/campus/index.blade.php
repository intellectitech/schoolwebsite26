<x-app-layout :title="'Campus'" :active="'campus'">

    {{-- ============ HERO ============ --}}
    <section class="relative h-[440px] md:h-[520px] overflow-hidden">
        <img
            src="https://images.unsplash.com/photo-1541339907198-e08756dedf3f?q=80&w=2000"
            alt="Campus pathway lined with historic buildings"
            class="absolute inset-0 w-full h-full object-cover"
        >
        <div class="absolute inset-0 hero-gradient"></div>

        <div class="relative h-full max-w-container mx-auto px-6 md:px-12 flex flex-col justify-center">
            <span class="inline-block w-fit bg-secondary-fixed text-on-secondary-fixed font-label-md text-label-md px-3 py-1 rounded-full mb-4">
                Experience Excellence
            </span>
            <h1 class="font-display-lg-mobile md:font-display-lg text-display-lg-mobile md:text-display-lg text-white max-w-2xl">
                A Vibrant Community Rooted in Tradition.
            </h1>
            <p class="font-body-lg text-body-lg text-white/85 max-w-xl mt-4">
                Discover a campus where intellectual rigor meets creative expression, and lifelong friendships are forged through shared discovery and purpose.
            </p>
        </div>
    </section>

    {{-- ============ STUDENT LIFE IN MOTION ============ --}}
    <section class="py-section-padding-lg">
        <div class="max-w-container mx-auto px-6 md:px-12">
            <div class="text-center mb-12">
                <h2 class="font-headline-lg text-headline-lg text-on-surface">Student Life in Motion</h2>
                <div class="w-16 h-0.5 bg-secondary-fixed mx-auto mt-4 mb-4"></div>
                <p class="font-body-md text-on-surface-variant max-w-xl mx-auto">
                    From the stadium lights to the studio stage, life at Starlight is a tapestry of diverse passions and collective growth.
                </p>
            </div>

            <div class="grid grid-cols-1 gap-6 md:grid-cols-2 lg:grid-cols-4">
                @foreach($galleryItems->where('title', '!=', 'Athletics') as $item)
                    <a href="#" class="group block overflow-hidden rounded-[28px] shadow-lg transition duration-300 hover:-translate-y-1 hover:shadow-2xl {{ $loop->first ? 'lg:col-span-2 lg:row-span-2' : '' }}">
                        <div class="relative aspect-[4/5] lg:aspect-[3/4]">
                            <img
                                src="{{ $item->image_url }}"
                                alt="{{ $item->title }}"
                                class="absolute inset-0 w-full h-full object-cover transition-transform duration-700 group-hover:scale-105"
                            >
                            <div class="absolute inset-0 bg-gradient-to-t from-black/80 via-black/10 to-transparent"></div>
                            <div class="absolute bottom-0 left-0 right-0 p-6">
                                <p class="text-xs uppercase tracking-[0.24em] text-white/70 mb-3">{{ $item->tagline }}</p>
                                <h4 class="font-headline-md text-headline-md text-white">{{ $item->title }}</h4>
                            </div>
                        </div>
                    </a>
                @endforeach
            </div>
        </div>
    </section>

    {{-- ============ WORLD-CLASS FACILITIES ============ --}}
    <section class="bg-surface-container py-section-padding-lg">
        <div class="max-w-container mx-auto px-6 md:px-12 grid grid-cols-1 lg:grid-cols-2 gap-10 items-center">
            <div>
                <h2 class="font-headline-lg text-headline-lg text-on-surface mb-4">World-Class Facilities</h2>
                <p class="font-body-md text-on-surface-variant mb-6">
                    Our campus is more than just a place to study; it's a dynamic ecosystem designed to catalyze breakthroughs, foster creativity, and provide every resource needed for global leadership.
                </p>

                <div class="flex flex-col gap-4">
                    @if($featuredFacility)
                        <div class="flex items-start gap-3">
                            <x-icon name="menu_book" class="text-secondary flex-shrink-0" />
                            <div>
                                <p class="font-body-md text-sm font-semibold text-on-surface">{{ $featuredFacility->name }}</p>
                                <p class="text-xs text-on-surface-variant">{{ $featuredFacility->description }}</p>
                            </div>
                        </div>
                    @endif
                    @foreach($secondaryFacilities as $facility)
                        <div class="flex items-start gap-3">
                            <x-icon
                                :name="$loop->first ? 'science' : 'sports_gymnastics'"
                                class="text-secondary flex-shrink-0"
                            />
                            <div>
                                <p class="font-body-md text-sm font-semibold text-on-surface">{{ $facility->name }}</p>
                                <p class="text-xs text-on-surface-variant">{{ $facility->description }}</p>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

            {{-- Image cluster: large featured facility + two stacked smaller ones --}}
            <div class="grid grid-cols-2 gap-4">
                @if($featuredFacility)
                    <a href="#" class="col-span-2 relative rounded-xl overflow-hidden h-48 group">
                        <img
                            src="{{ $featuredFacility->image_url }}"
                            alt="{{ $featuredFacility->name }}"
                            class="absolute inset-0 w-full h-full object-cover group-hover:scale-105 transition-transform duration-500"
                        >
                        <div class="absolute inset-0 bg-gradient-to-t from-black/70 to-transparent"></div>
                        <p class="absolute bottom-3 left-4 font-body-lg text-body-lg !text-base text-white font-semibold">
                            {{ $featuredFacility->name }}
                        </p>
                    </a>
                @endif

                @foreach($secondaryFacilities as $facility)
                    <a href="#" class="relative rounded-xl overflow-hidden h-32 group">
                        <img
                            src="{{ $facility->image_url }}"
                            alt="{{ $facility->name }}"
                            class="absolute inset-0 w-full h-full object-cover group-hover:scale-105 transition-transform duration-500"
                        >
                        <div class="absolute inset-0 bg-gradient-to-t from-black/70 to-transparent"></div>
                        <p class="absolute bottom-2 left-3 font-body-md text-xs text-white font-semibold">
                            {{ $facility->name }}
                        </p>
                    </a>
                @endforeach
            </div>
        </div>
    </section>

    {{-- ============ STUDENT STORIES ============ --}}
    <section class="py-section-padding-lg">
        <div class="max-w-container mx-auto px-6 md:px-12">
            <div class="text-center mb-12">
                <h2 class="font-headline-lg text-headline-lg text-on-surface">Student Stories</h2>
                <p class="font-body-md text-on-surface-variant italic mt-2">Voices of the Starlight community</p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                @forelse($testimonials as $testimonial)
                    <x-testimonial-card :testimonial="$testimonial" />
                @empty
                    <p class="text-on-surface-variant text-center col-span-2">Student stories coming soon.</p>
                @endforelse
            </div>
        </div>
    </section>

    {{-- ============ BEGIN YOUR JOURNEY CTA ============ --}}
    <section class="py-section-padding-lg bg-surface-bright">
        <div class="max-w-container mx-auto px-6 md:px-12">
            <div class="bg-primary rounded-xl px-8 py-12 md:py-16 text-center">
                <h2 class="font-headline-lg text-headline-lg text-white mb-3">Begin Your Journey Today</h2>
                <p class="font-body-md text-white/70 max-w-lg mx-auto mb-8">
                    Join a community of scholars, athletes, and artists dedicated to excellence and leadership.
                </p>
                <div class="flex flex-wrap justify-center gap-4">
                    <x-button variant="gold" href="{{ route('admissions') }}">Apply Now</x-button>
                    <x-button variant="outline-white" href="#">Visit Campus</x-button>
                </div>
            </div>
        </div>
    </section>

</x-app-layout>