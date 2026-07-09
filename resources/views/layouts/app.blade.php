<!DOCTYPE html>
<html lang="en" class="light">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title ?? 'Starlight Academy' }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-background text-on-background font-body-md selection:bg-secondary-container selection:text-on-secondary-container antialiased">

    <x-public.navbar :active="$active ?? null" />

    <main>
        {{ $slot }}
    </main>

    <x-public.footer />
    <x-public.mobile-nav :active="$active ?? null" />

</body>
</html>