<x-app-layout :title="'Contact'" :active="'contact'">

    {{-- ============ HERO ============ --}}
    <section class="relative h-[380px] md:h-[460px] overflow-hidden">
        <img
            src="https://images.unsplash.com/photo-1607237138185-eedd9c632b0b?q=80&w=2000"
            alt="Lyceum Academy entrance gates"
            class="absolute inset-0 w-full h-full object-cover"
        >
        <div class="absolute inset-0 bg-primary/40"></div>

        <div class="relative h-full max-w-4xl mx-auto px-6 flex flex-col items-center justify-center text-center">
            <h1 class="font-display-lg-mobile md:font-display-lg text-display-lg-mobile md:text-display-lg text-white mb-4">
                Get in Touch
            </h1>
            <div class="w-24 h-1 bg-secondary-fixed mx-auto mb-6 rounded-full"></div>
            <p class="font-body-lg text-body-lg text-white/90 max-w-2xl">
                We are here to assist you in your journey towards academic excellence. Reach out to our team for any inquiries regarding admissions, curriculum, or campus life.
            </p>
        </div>
    </section>

    {{-- ============ FLASH MESSAGE ============ --}}
    @if(session('success'))
        <div class="max-w-container mx-auto px-6 md:px-12 mt-8">
            <div class="flex items-center gap-3 bg-secondary-fixed/20 border border-secondary-fixed/40 text-on-secondary-fixed rounded-lg px-4 py-3">
                <x-icon name="check_circle" class="text-secondary" />
                <p class="font-body-md text-sm">{{ session('success') }}</p>
            </div>
        </div>
    @endif

    {{-- ============ MAIN CONTENT ============ --}}
    <section class="max-w-container mx-auto px-6 md:px-12 py-section-padding-lg">
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-12">

            {{-- ---------- LEFT COLUMN: Campus Information ---------- --}}
            <div class="lg:col-span-5 space-y-10">
                <div>
                    <h2 class="font-headline-md text-headline-md text-primary mb-6 border-b-2 border-secondary-fixed w-max pb-1">
                        Campus Information
                    </h2>

                    <div class="space-y-8 mt-8">
                        <div class="flex gap-4">
                            <x-icon name="location_on" class="text-secondary !text-2xl flex-shrink-0" />
                            <div>
                                <h4 class="font-label-md text-label-md text-primary uppercase tracking-wider mb-2">Physical Address</h4>
                                <p class="font-body-md text-sm text-on-surface-variant">
                                    1200 Academic Plaza, University Heights<br>
                                    Cambridge, MA 02138, United States
                                </p>
                            </div>
                        </div>

                        <div class="flex gap-4">
                            <x-icon name="schedule" class="text-secondary !text-2xl flex-shrink-0" />
                            <div>
                                <h4 class="font-label-md text-label-md text-primary uppercase tracking-wider mb-2">Office Hours</h4>
                                {{-- Static — 3 fixed rows, same reasoning as Tuition & Fees in the Admissions page. --}}
                                <div class="text-sm text-on-surface-variant space-y-1">
                                    <div class="flex justify-between max-w-xs"><span>Monday – Friday</span><span class="font-semibold text-on-surface">8:00 AM – 6:00 PM</span></div>
                                    <div class="flex justify-between max-w-xs"><span>Saturday</span><span class="font-semibold text-on-surface">9:00 AM – 1:00 PM</span></div>
                                    <div class="flex justify-between max-w-xs"><span>Sunday</span><span class="font-semibold text-on-surface">Closed</span></div>
                                </div>
                            </div>
                        </div>

                        {{-- Department directory — database-driven, includes the emergency Campus Security block --}}
                        <div class="flex gap-4">
                            <x-icon name="call" class="text-secondary !text-2xl flex-shrink-0" />
                            <div class="flex-1">
                                <h4 class="font-label-md text-label-md text-primary uppercase tracking-wider mb-3">Departmental Directories</h4>
                                <div class="flex flex-col gap-3">
                                    @foreach($departments as $department)
                                        <x-department-card :department="$department" />
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Map — static image + external Google Maps link; no API key required --}}
                <a
                    href="https://www.google.com/maps/search/?api=1&query=1200+Academic+Plaza+Cambridge+MA+02138"
                    target="_blank" rel="noopener"
                    class="block rounded-xl overflow-hidden shadow-md border border-outline-variant/50 group h-56 relative"
                >
                    <img
                        src="https://images.unsplash.com/photo-1524661135-423995f22d0b?q=80&w=1200"
                        alt="Map of the campus area"
                        class="absolute inset-0 w-full h-full object-cover transition-transform duration-700 group-hover:scale-105"
                    >
                    <div class="absolute bottom-4 left-4 bg-white/90 px-4 py-2 rounded-lg shadow-lg flex items-center gap-2">
                        <x-icon name="directions" class="text-primary !text-base" />
                        <span class="text-xs font-bold text-primary">Open in Google Maps</span>
                    </div>
                </a>
            </div>

            {{-- ---------- RIGHT COLUMN: Form ---------- --}}
            <div class="lg:col-span-7">
                <div class="bg-surface-container-lowest p-8 md:p-12 rounded-xl shadow-sm border border-outline-variant/20">
                    <h2 class="font-headline-md text-headline-md text-primary mb-8">Send Us a Message</h2>

                    <form method="POST" action="{{ route('contact.store') }}" class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        @csrf

                        <div class="space-y-2">
                            <x-input-label for="full_name" value="Full Name" />
                            <x-text-input id="full_name" name="full_name" type="text" :value="old('full_name')" placeholder="John Doe" required />
                            <x-input-error :messages="$errors->get('full_name')" />
                        </div>

                        <div class="space-y-2">
                            <x-input-label for="email" value="Email Address" />
                            <x-text-input id="email" name="email" type="email" :value="old('email')" placeholder="john@example.com" required />
                            <x-input-error :messages="$errors->get('email')" />
                        </div>

                        <div class="md:col-span-2 space-y-2">
                            <x-input-label for="subject" value="Area of Interest" />
                            <select
                                id="subject" name="subject"
                                class="w-full bg-surface-bright border border-outline-variant rounded-lg px-4 py-3 font-body-md text-sm focus:outline-none focus:ring-2 focus:ring-primary focus:border-primary appearance-none cursor-pointer"
                            >
                                <option value="">Select an option</option>
                                @foreach([
                                    'General Inquiry',
                                    'Admissions & Enrollment',
                                    'Academics & Curriculum',
                                    'Careers & Employment',
                                    'Other Inquiries',
                                ] as $option)
                                    <option value="{{ $option }}" @selected(old('subject') === $option)>{{ $option }}</option>
                                @endforeach
                            </select>
                            <x-input-error :messages="$errors->get('subject')" />
                        </div>

                        <div class="md:col-span-2 space-y-2">
                            <x-input-label for="message" value="Your Message" />
                            <textarea
                                id="message" name="message" rows="5" required
                                placeholder="How can we help you today?"
                                class="w-full bg-surface-bright border border-outline-variant rounded-lg px-4 py-3 font-body-md text-sm focus:outline-none focus:ring-2 focus:ring-primary focus:border-primary"
                            >{{ old('message') }}</textarea>
                            <x-input-error :messages="$errors->get('message')" />
                        </div>

                        <div class="md:col-span-2 mt-4">
                            <x-button type="submit" variant="primary" class="w-full !py-4">
                                Send Message <x-icon name="send" class="!text-xl" />
                            </x-button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </section>

    {{-- ============ SECONDARY: FAQ & VISIT CTA ============ --}}
    <section class="bg-surface-container py-section-padding-lg">
        <div class="max-w-container mx-auto px-6 md:px-12 grid grid-cols-1 md:grid-cols-2 gap-8">

            <div class="bg-surface-bright p-10 rounded-xl flex flex-col items-center text-center hover:shadow-xl transition-all duration-300">
                <div class="w-16 h-16 bg-primary-fixed rounded-full flex items-center justify-center mb-6 text-primary">
                    <x-icon name="quiz" class="!text-4xl" />
                </div>
                <h3 class="font-headline-md text-headline-md text-primary mb-4">Quick Answers</h3>
                <p class="font-body-md text-sm text-on-surface-variant mb-8 max-w-sm">
                    Save time by exploring our frequently asked questions about admissions, student life, and scholarships.
                </p>
                <a href="{{ route('admissions') }}#faq" class="inline-flex items-center gap-2 text-primary font-bold hover:underline">
                    Browse FAQs <x-icon name="arrow_forward" class="!text-base" />
                </a>
            </div>

            <div class="bg-primary text-white p-10 rounded-xl flex flex-col items-center text-center hover:shadow-xl transition-all duration-300">
                <div class="w-16 h-16 bg-secondary-fixed rounded-full flex items-center justify-center mb-6 text-primary">
                    <x-icon name="tour" class="!text-4xl" :filled="true" />
                </div>
                <h3 class="font-headline-md text-headline-md mb-4">Experience Starlight</h3>
                <p class="font-body-md text-sm text-white/80 mb-8 max-w-sm">
                    The best way to understand our culture is to walk through our halls. Schedule a private tour or join an open day.
                </p>
                <x-button variant="gold" href="#" class="!rounded-full">Schedule a Tour</x-button>
            </div>
        </div>
    </section>

</x-app-layout>
