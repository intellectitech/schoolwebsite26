@props(['type' => 'submit'])

<button
    type="{{ $type }}"
    {{ $attributes->merge([
        'class' => 'w-full inline-flex items-center justify-center gap-2 bg-primary text-on-primary
                    font-label-md text-label-md px-6 py-3.5 rounded-lg transition-all
                    hover:bg-primary-container active:scale-[0.98] shadow-md
                    disabled:opacity-50 disabled:cursor-not-allowed'
    ]) }}
>
    {{ $slot }}
</button>