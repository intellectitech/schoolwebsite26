@props(['heading' => 'Dashboard'])

<header class="h-16 bg-surface-bright border-b border-outline-variant/30 flex items-center justify-between px-6 sticky top-0 z-20">
    <div class="flex items-center gap-4">
        <button @click="$store.sidebar.open = !$store.sidebar.open" class="md:hidden">
            <x-icon name="menu" class="text-primary text-2xl" />
        </button>
        <h1 class="font-headline-md text-headline-md text-primary">{{ $heading }}</h1>
    </div>

    <div class="flex items-center gap-4">
        <button class="relative" title="Notifications">
            <x-icon name="notifications" class="text-on-surface-variant text-2xl" />
            <span class="absolute -top-0.5 -right-0.5 w-2.5 h-2.5 bg-error rounded-full"></span>
        </button>

        <div x-data="{ open: false }" class="relative">
            <button @click="open = !open" class="flex items-center gap-2">
                <div class="w-9 h-9 rounded-full bg-primary text-white flex items-center justify-center font-label-md text-label-md">
                    {{ strtoupper(substr(auth()->user()->name ?? 'A', 0, 1)) }}
                </div>
            </button>

            <div
                x-show="open" @click.outside="open = false" x-transition
                class="absolute right-0 mt-2 w-48 bg-white rounded-lg shadow-lg border border-outline-variant/20 py-2"
                style="display: none;"
            >
                <div class="px-4 py-2 border-b border-outline-variant/20">
                    <p class="font-label-md text-label-md text-on-surface">{{ auth()->user()->name ?? 'Admin' }}</p>
                    <p class="font-caption text-caption text-on-surface-variant">{{ auth()->user()->email ?? '' }}</p>
                </div>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="w-full text-left px-4 py-2 font-body-md text-sm text-error hover:bg-error-container/30">
                        Log out
                    </button>
                </form>
            </div>
        </div>
    </div>
</header>