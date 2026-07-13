@props(['active' => null])

<header
    x-data="{ scrolled: false, mobileMenuOpen: false }"
    x-init="window.addEventListener('scroll', () => scrolled = window.scrollY > 50)"
    :class="scrolled ? 'shadow-md bg-opacity-95 backdrop-blur-md' : ''"
    class="fixed top-0 w-full z-50 bg-surface-bright transition-all duration-300"
>
    <div class="flex justify-between items-center px-6 md:px-12 h-16">
        <div class="flex items-center gap-4">
            <button
                type="button"
                @click="mobileMenuOpen = !mobileMenuOpen"
                class="md:hidden text-primary"
                aria-label="Toggle navigation menu"
                :aria-expanded="mobileMenuOpen"
            >
                <x-icon name="menu" class="cursor-pointer" />
            </button>
            <a href="{{ route('home') }}" class="font-headline-md text-headline-md text-primary tracking-tight">
                Starlight Academy
            </a>
        </div>

    <nav class="hidden md:flex items-center gap-8">
        @php
            $links = [
                'home' => ['label' => 'Home', 'route' => 'home'],
                'academics' => ['label' => 'Academics', 'route' => 'academics'],
                'admissions' => ['label' => 'Admissions', 'route' => 'admissions'],
                'campus' => ['label' => 'Campus', 'route' => 'campus'],
                'contact' => ['label' => 'Contact', 'route' => 'contact.show'],
            ];
        @endphp

        @foreach($links as $key => $link)
            <a
                href="{{ route($link['route']) }}"
                class="font-label-md text-label-md transition-opacity hover:opacity-80
                    {{ $active === $key ? 'text-primary border-b-2 border-primary pb-1' : 'text-on-surface-variant' }}"
            >
                {{ $link['label'] }}
            </a>
        @endforeach
    </nav>

        <div class="flex items-center gap-4">
            <x-button href="{{ route('admissions') }}" variant="gold" class="!px-6 !py-2.5 hidden sm:inline-flex">
                Apply Now
            </x-button>
            <a href="{{ route('admin.dashboard') }}" title="Staff Login">
                <x-icon name="account_circle" class="text-primary text-2xl" />
            </a>
        </div>
    </div>

    <div
        x-show="mobileMenuOpen"
        x-transition
        x-on:click.away="mobileMenuOpen = false"
        class="md:hidden border-t border-outline-variant/20 bg-surface-bright shadow-lg"
    >
        <div class="px-6 py-4 flex flex-col gap-2">
            @foreach($links as $key => $link)
                <a
                    href="{{ route($link['route']) }}"
                    @click="mobileMenuOpen = false"
                    class="font-label-md text-label-md py-2 transition-opacity hover:opacity-80
                        {{ $active === $key ? 'text-primary' : 'text-on-surface-variant' }}"
                >
                    {{ $link['label'] }}
                </a>
            @endforeach
        </div>
    </div>
</header>

{{-- Spacer so fixed header doesn't overlap page content --}}
<div class="h-16"></div>