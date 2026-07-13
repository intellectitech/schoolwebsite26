<x-app-layout :title="'Admissions'" :active="'admissions'">

    {{-- ============ HERO ============ --}}
    <section class="relative h-[380px] md:h-[440px] overflow-hidden">
        <img
            src="https://images.unsplash.com/photo-1541339907198-e08756dedf3f?q=80&w=2000"
            alt="Starlight Academy building columns"
            class="absolute inset-0 w-full h-full object-cover"
        >
        <div class="absolute inset-0 hero-gradient"></div>

        <div class="relative h-full max-w-container mx-auto px-6 md:px-12 flex flex-col items-center justify-center text-center">
            <h1 class="font-display-lg-mobile md:font-display-lg text-display-lg-mobile md:text-display-lg text-white max-w-2xl">
                Your Journey of Excellence Begins Here
            </h1>
            <p class="font-body-lg text-body-lg text-white/85 max-w-xl mt-4">
                Join a legacy of intellectual ambition and character. Our admissions process is designed to identify students who are ready to lead and innovate.
            </p>
        </div>
    </section>

    {{-- ============ ADMISSION PROCESS ============ --}}
    <section class="py-section-padding-lg">
        <div class="max-w-container mx-auto px-6 md:px-12">
            <div class="text-center mb-16">
                <x-section-eyebrow text="The Pathway" class="justify-center flex" />
                <h2 class="font-headline-lg text-headline-lg text-on-surface">Admission Process</h2>
                <div class="w-16 h-0.5 bg-secondary-fixed mx-auto mt-4"></div>
            </div>

            <div class="flex flex-col md:flex-row gap-10 md:gap-4">
                @foreach($steps as $step)
                    <x-process-step :step="$step" :is-last="$loop->last" />
                @endforeach
            </div>
        </div>
    </section>

    {{-- ============ TUITION & FINANCIAL AID ============ --}}
    <section class="bg-surface-container py-section-padding-lg">
        <div class="max-w-container mx-auto px-6 md:px-12 grid grid-cols-1 md:grid-cols-2 gap-6">

            {{-- Tuition & Fees --}}
            <div class="bg-white rounded-xl overflow-hidden">
                <div class="relative h-40">
                    <img
                        src="https://images.unsplash.com/photo-1454165804606-c3d57bc86b40?q=80&w=800"
                        alt="Hand writing with a pen"
                        class="absolute inset-0 w-full h-full object-cover"
                    >
                    <div class="absolute inset-0 bg-gradient-to-t from-black/70 to-transparent"></div>
                    <h3 class="absolute bottom-4 left-6 font-headline-md text-headline-md !text-xl text-white">Tuition & Fees</h3>
                </div>
                <div class="p-6">
                    <p class="font-body-md text-sm text-on-surface-variant mb-4">
                        Transparent pricing reflecting our commitment to world-class faculty and state-of-the-art campus facilities.
                    </p>

                    {{-- Static fee schedule: page-specific figures reviewed annually by admissions staff,
                         not a repeating admin-managed list — kept inline for now. --}}
                    <x-fee-row label="Annual Tuition" amount="$42,500" />
                    <x-fee-row label="Campus Services" amount="$3,200" />
                    <x-fee-row label="Resource Fee" amount="$1,500" />

                    <x-button variant="primary" href="{{ route('apply.create') }}" class="w-full mt-6 !py-3">
                        Download Full Fee Schedule
                    </x-button>
                </div>
            </div>

            {{-- Financial Aid --}}
            <div class="bg-white rounded-xl overflow-hidden">
                <div class="relative h-40">
                    <img
                        src="https://images.unsplash.com/photo-1523240795612-9a054b0db644?q=80&w=800"
                        alt="Students gathered in a bright hall"
                        class="absolute inset-0 w-full h-full object-cover"
                    >
                    <div class="absolute inset-0 bg-gradient-to-t from-black/70 to-transparent"></div>
                    <h3 class="absolute bottom-4 left-6 font-headline-md text-headline-md !text-xl text-white">Financial Aid</h3>
                </div>
                <div class="p-6">
                    <p class="font-body-md text-sm text-on-surface-variant mb-4">
                        We believe financial circumstances should not be a barrier to exceptional talent and ambition.
                    </p>

                    <div class="bg-secondary-fixed/20 rounded-lg p-4 mb-4 flex items-center gap-3">
                        <x-icon name="verified" class="text-secondary-fixed text-2xl" />
                        <p class="font-body-md text-sm text-on-surface">
                            <span class="font-bold">65% of Students</span> receive some form of scholarship or merit-based assistance annually.
                        </p>
                    </div>

                    <div class="flex flex-col gap-3 mb-2">
                        <div class="flex items-start gap-3">
                            <x-icon name="volunteer_activism" class="text-primary flex-shrink-0" />
                            <div>
                                <p class="font-body-md text-sm font-semibold text-on-surface">Need-Based Grants</p>
                                <p class="text-xs text-on-surface-variant">Tailored support based on family financial profiles.</p>
                            </div>
                        </div>
                        <div class="flex items-start gap-3">
                            <x-icon name="military_tech" class="text-primary flex-shrink-0" />
                            <div>
                                <p class="font-body-md text-sm font-semibold text-on-surface">Merit Scholarships</p>
                                <p class="text-xs text-on-surface-variant">Awarded for academic, athletic, or artistic excellence.</p>
                            </div>
                        </div>
                    </div>

                    <x-button variant="outline" href="{{ route('apply.create') }}" class="w-full mt-4 !py-3">
                        Explore Aid Options
                    </x-button>
                </div>
            </div>
        </div>
    </section>

    {{-- ============ FAQ ============ --}}
    <section class="py-section-padding-lg">
        <div class="max-w-[720px] mx-auto px-6">
            <div class="text-center mb-10">
                <h2 class="font-headline-lg text-headline-lg text-on-surface">Frequently Asked Questions</h2>
                <p class="font-body-md text-on-surface-variant mt-2">
                    Find answers to common questions about our enrollment cycle.
                </p>
            </div>

            <div class="flex flex-col gap-3">
                @forelse($faqs as $faq)
                    <x-faq-accordion-item :faq="$faq" />
                @empty
                    <p class="text-on-surface-variant text-center">No FAQs available yet.</p>
                @endforelse
            </div>
        </div>
    </section>

    {{-- ============ READY TO SHAPE YOUR FUTURE CTA ============ --}}
    <section class="relative bg-primary py-section-padding-lg overflow-hidden">
        {{-- Faint decorative graduation-cap glyph, echoes the screenshot's watermark shape --}}
        <x-icon
            name="school"
            class="absolute -right-10 -bottom-10 text-white/5 pointer-events-none select-none"
            style="font-size: 260px;"
        />

        <div class="relative max-w-container mx-auto px-6 md:px-12 text-center">
            <h2 class="font-headline-lg text-headline-lg text-white mb-8">Ready to Shape Your Future?</h2>
            <div class="flex flex-wrap justify-center gap-4">
                <x-button variant="gold" href="{{ route('apply.create') }}">Apply Now</x-button>
                <x-button variant="outline-white" href="{{ route('apply.create') }}">Request Information</x-button>
            </div>
        </div>
    </section>

</x-app-layout>