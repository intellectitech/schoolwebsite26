@props(['active' => null])

@php
    $nav = [
    'dashboard' => ['label' => 'Dashboard', 'icon' => 'space_dashboard', 'route' => 'admin.dashboard'],
    'applications' => ['label' => 'Applications', 'icon' => 'assignment_ind', 'route' => 'admin.applications.index'],
    'messages' => ['label' => 'Messages', 'icon' => 'mail', 'route' => 'admin.messages.index'],
    'news' => ['label' => 'News Posts', 'icon' => 'newspaper', 'route' => 'admin.news.index'],
    'events' => ['label' => 'Events', 'icon' => 'event', 'route' => 'admin.events.index'],
    'pathways' => ['label' => 'Pathways', 'icon' => 'route', 'route' => 'admin.pathways.index'],
    'faculty' => ['label' => 'Faculty', 'icon' => 'groups', 'route' => 'admin.faculty.index'],
    'testimonials' => ['label' => 'Testimonials', 'icon' => 'format_quote', 'route' => 'admin.testimonials.index'],
    'gallery' => ['label' => 'Campus Gallery', 'icon' => 'photo_library', 'route' => 'admin.gallery.index'],
    'facilities' => ['label' => 'Facilities', 'icon' => 'apartment', 'route' => 'admin.facilities.index'],
    'faqs' => ['label' => 'FAQs', 'icon' => 'quiz', 'route' => 'admin.faqs.index'],
    'admission-steps' => ['label' => 'Admission Steps', 'icon' => 'how_to_reg', 'route' => 'admin.admission-steps.index'],
];
@endphp

{{-- Mobile: drawer toggled via Alpine; Desktop: static column --}}
<aside
    x-data
    :class="$store.sidebar.open ? 'translate-x-0' : '-translate-x-full'"
    class="fixed md:static inset-y-0 left-0 w-64 bg-primary text-white flex flex-col z-40 transition-transform duration-300 md:translate-x-0"
>
    <div class="h-16 flex items-center px-6 border-b border-white/10">
        <span class="font-headline-md text-headline-md text-secondary-fixed">Starlight</span>
        <span class="font-label-md text-label-md text-white/60 ml-2">Admin</span>
    </div>

    <nav class="flex-1 overflow-y-auto py-6 px-3 space-y-1">
        @foreach($nav as $key => $item)
            <a
                href="{{ route($item['route']) }}"
                class="flex items-center gap-3 px-4 py-3 rounded-lg font-label-md text-label-md transition-colors
                    {{ $active === $key
                        ? 'bg-secondary-fixed text-on-secondary-fixed'
                        : 'text-white/80 hover:bg-white/10' }}"
            >
                <x-icon :name="$item['icon']" class="text-xl" />
                {{ $item['label'] }}
            </a>
        @endforeach
    </nav>

    <div class="p-4 border-t border-white/10">
        <a href="{{ route('home') }}" class="flex items-center gap-3 px-4 py-3 rounded-lg text-white/70 hover:bg-white/10 font-label-md text-label-md">
            <x-icon name="public" class="text-xl" />
            View Public Site
        </a>
    </div>
</aside>

{{-- Mobile overlay --}}
<div
    x-show="$store.sidebar.open"
    @click="$store.sidebar.open = false"
    x-transition.opacity
    class="fixed inset-0 bg-black/40 z-30 md:hidden"
    style="display: none;"
></div>