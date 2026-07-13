<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title ?? 'Admin' }} | Starlight Academy</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body
    x-data
    x-init="Alpine.store('sidebar', { open: false })"
    class="bg-surface-container-low text-on-background font-body-md antialiased"
>
    <div class="flex min-h-screen">
        <x-admin.sidebar :active="$active ?? null" />

        <div class="flex-1 flex flex-col min-w-0">
            <x-admin.topbar :heading="$title ?? 'Dashboard'" />

            <main class="flex-1 p-6">
                {{ $slot }}
            </main>

            <footer class="px-6 py-4 text-center font-caption text-caption text-on-surface-variant border-t border-outline-variant/20">
                &copy; {{ now()->year }} Starlight Academy Admin
            </footer>
        </div>
    </div>
</body>
</html>