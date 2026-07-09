<x-app-layout :title="'Academics'" :active="'academics'">

    {{-- ============ HERO ============ --}}
    <section class="relative h-[420px] md:h-[480px] overflow-hidden">
        <img
            src="https://images.unsplash.com/photo-1521587760476-6c12a4b040da?q=80&w=2000"
            alt="Starlight Academy library interior"
            class="absolute inset-0 w-full h-full object-cover"
        >
        <div class="absolute inset-0 hero-gradient"></div>

        <div class="relative h-full max-w-container mx-auto px-6 md:px-12 flex flex-col justify-center">
            <h1 class="font-display-lg-mobile md:font-display-lg text-display-lg-mobile md:text-display-lg text-white max-w-2xl">
                Excellence in Global Education
            </h1>
            <p class="font-body-lg text-body-lg text-white/85 max-w-xl mt-4">
                From early childhood through high school, Starlight Academy cultivates intellectual curiosity, critical thinking, and a lifelong passion for discovery.
            </p>
            <div class="flex flex-wrap gap-4 mt-8">
                <x-button variant="gold" href="{{ route('admissions') }}">Apply for 2024</x-button>
                <x-button variant="outline-white" href="#">Explore Programs</x-button>
            </div>
        </div>
    </section>

    {{-- ============ EDUCATIONAL JOURNEY ============ --}}
    <section class="py-section-padding-lg">
        <div class="max-w-container mx-auto px-6 md:px-12">
            <div class="text-center mb-12">
                <x-section-eyebrow text="Our Pathways" class="justify-center flex" />
                <h2 class="font-headline-lg text-headline-lg text-on-surface">The Educational Journey</h2>
                <div class="w-16 h-0.5 bg-secondary-fixed mx-auto mt-4"></div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
                @foreach($pathways as $pathway)
                    <x-pathway-card :pathway="$pathway" />
                @endforeach
            </div>
        </div>
    </section>

    {{-- ============ GLOBAL CURRICULUM ============ --}}
    <section class="bg-primary py-section-padding-lg">
        <div class="max-w-container mx-auto px-6 md:px-12 grid grid-cols-1 lg:grid-cols-2 gap-10 items-center">
            {{-- Image with floating stat badge --}}
            <div class="relative">
                <img
                    src="https://images.unsplash.com/photo-1519389950473-47ba0277781c?q=80&w=1000"
                    alt="Desk with notebook, pen, and compass"
                    class="rounded-xl w-full h-80 object-cover"
                >
                <div class="absolute -bottom-6 left-6 bg-secondary-fixed text-on-secondary-fixed rounded-xl px-6 py-4 shadow-lg max-w-[220px]">
                    <p class="text-3xl font-bold leading-none">98%</p>
                    <p class="text-xs font-label-md mt-1">University Placement Rate for 2023 Graduates</p>
                </div>
            </div>

            <div>
                <p class="font-label-md text-label-md text-secondary-fixed uppercase tracking-wider mb-2">World Standard</p>
                <h2 class="font-headline-lg text-headline-lg text-white mb-4">A Global Curriculum for a Digital Age</h2>
                <p class="font-headline-md text-headline-md !text-lg italic text-white/70 border-l-2 border-secondary-fixed pl-4 mb-8">
                    "We don't just teach subjects; we teach students how to think, adapt, and lead in an increasingly complex global landscape."
                </p>

                <div class="flex flex-col gap-5">
                    <x-checklist-item
                        title="Multilingual Immersion"
                        description="Students master three languages by graduation, ensuring global mobility."
                    />
                    <x-checklist-item
                        title="STEM & Arts Integration"
                        description="A holistic approach that bridges technical skill with creative expression."
                    />
                    <x-checklist-item
                        title="Experiential Learning"
                        description="Quarterly field residencies that apply classroom theories to real-world problems."
                    />
                </div>
            </div>
        </div>
    </section>

    {{-- ============ FACULTY SPOTLIGHT ============ --}}
    <section class="py-section-padding-lg" x-data="{
        scroll(direction) {
            const el = $refs.track;
            el.scrollBy({ left: direction * (el.clientWidth * 0.8), behavior: 'smooth' });
        }
    }">
        <div class="max-w-container mx-auto px-6 md:px-12">
            <div class="flex items-end justify-between mb-8">
                <div>
                    <x-section-eyebrow text="Our Mentors" />
                    <h2 class="font-headline-lg text-headline-lg text-on-surface">Faculty Spotlight</h2>
                    <p class="font-body-md text-on-surface-variant mt-2 max-w-lg">
                        Led by world-class educators, researchers, and practitioners who are dedicated to unlocking every student's potential.
                    </p>
                </div>

                {{-- Carousel controls (desktop) --}}
                <div class="hidden md:flex gap-2 flex-shrink-0">
                    <button
                        @click="scroll(-1)"
                        class="w-10 h-10 rounded-full border border-outline-variant flex items-center justify-center hover:bg-primary/5 transition-colors"
                        aria-label="Previous"
                    >
                        <x-icon name="chevron_left" />
                    </button>
                    <button
                        @click="scroll(1)"
                        class="w-10 h-10 rounded-full border border-outline-variant flex items-center justify-center hover:bg-primary/5 transition-colors"
                        aria-label="Next"
                    >
                        <x-icon name="chevron_right" />
                    </button>
                </div>
            </div>

            <div
                x-ref="track"
                class="flex gap-6 overflow-x-auto no-scrollbar scroll-smooth snap-x snap-mandatory pb-2"
            >
                @forelse($faculty as $member)
                    <div class="min-w-[220px] sm:min-w-[240px] snap-start">
                        <x-faculty-card :member="$member" />
                    </div>
                @empty
                    <p class="text-on-surface-variant">Faculty profiles coming soon.</p>
                @endforelse
            </div>
        </div>
    </section>

    {{-- ============ START THE JOURNEY CTA ============ --}}
    <section class="bg-surface-container py-section-padding-lg">
        <div class="max-w-[600px] mx-auto px-6 text-center">
            <h2 class="font-headline-lg text-headline-lg text-on-surface mb-3">Start the Journey Today</h2>
            <p class="font-body-md text-on-surface-variant mb-8">
                Schedule a campus tour or attend an upcoming virtual open house to see Starlight in action.
            </p>
            <div class="flex flex-wrap justify-center gap-4">
                <x-button variant="primary" href="#">Book a Campus Tour</x-button>
                <x-button variant="outline" href="#">Request Prospectus</x-button>
            </div>
        </div>
    </section>

</x-app-layout>