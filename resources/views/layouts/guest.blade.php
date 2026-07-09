<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ config('app.name', 'Starlight Academy') }} | Staff Login</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="font-body-md text-on-background antialiased">
    <div class="min-h-screen flex flex-col md:flex-row">

        {{-- Brand panel — hidden on mobile to keep the form full-width there --}}
        <div class="hidden md:flex md:w-1/2 bg-primary relative overflow-hidden items-center justify-center p-12">
            <img
                src="https://images.unsplash.com/photo-1568667256549-094345857637?q=80&w=1600"
                alt=""
                class="absolute inset-0 w-full h-full object-cover opacity-20"
            >
            <div class="relative text-center max-w-sm">
                <h1 class="font-headline-lg text-headline-lg !text-4xl text-secondary-fixed mb-4">
                    Starlight Academy
                </h1>
                <p class="text-white/70 font-body-lg text-body-lg">
                    Content management for the Starlight Academy public site.
                </p>
            </div>
        </div>

        {{-- Form panel --}}
        <div class="flex-1 flex items-center justify-center p-6 md:p-12 bg-surface-bright">
            <div class="w-full max-w-sm">
                <a href="{{ route('home') }}" class="md:hidden block text-center font-headline-md text-headline-md text-primary mb-8">
                    Starlight Academy
                </a>

                <div class="bg-white rounded-xl border border-outline-variant/20 shadow-[0_4px_24px_-4px_rgba(0,30,64,0.08)] p-8">
                    {{ $slot }}
                </div>
            </div>
        </div>

    </div>
</body>
</html>