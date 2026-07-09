@props(['testimonial'])

<div class="bg-white rounded-xl border border-outline-variant/20 p-6 flex flex-col gap-4">
    <x-icon name="format_quote" class="text-secondary-fixed !text-3xl" :filled="true" />

    <p class="font-body-lg text-body-lg !text-base text-on-surface italic leading-relaxed flex-1">
        "{{ $testimonial->quote }}"
    </p>

    <div class="flex items-center gap-3 pt-2 border-t border-outline-variant/10">
        @if($testimonial->photo_url)
            <img
                src="{{ $testimonial->photo_url }}"
                alt="{{ $testimonial->student_name }}"
                class="w-11 h-11 rounded-full object-cover flex-shrink-0"
            >
        @else
            <div class="w-11 h-11 rounded-full bg-primary/10 flex items-center justify-center text-primary font-semibold flex-shrink-0">
                {{ strtoupper(substr($testimonial->student_name, 0, 1)) }}
            </div>
        @endif
        <div>
            <p class="font-body-md text-sm font-semibold text-on-surface">{{ $testimonial->student_name }}</p>
            <p class="text-xs text-on-surface-variant">{{ $testimonial->student_class }}</p>
        </div>
    </div>
</div>