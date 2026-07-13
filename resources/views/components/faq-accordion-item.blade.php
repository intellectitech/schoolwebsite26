@props(['faq'])

<div x-data="{ open: false }" class="bg-white rounded-lg border border-outline-variant/20 overflow-hidden">
    <button
        @click="open = !open"
        class="w-full flex items-center justify-between gap-4 px-6 py-4 text-left"
    >
        <span class="font-body-lg text-body-lg !text-base font-semibold text-on-surface">{{ $faq->question }}</span>
        <x-icon
            name="expand_more"
            :class="'text-primary transition-transform duration-300 flex-shrink-0'"
            x-bind:style="open ? 'transform: rotate(180deg)' : ''"
        />
    </button>

    <div
        x-show="open"
        x-collapse
        class="px-6 pb-4"
        style="display: none;"
    >
        <p class="font-body-md text-sm text-on-surface-variant">{{ $faq->answer }}</p>
    </div>
</div>