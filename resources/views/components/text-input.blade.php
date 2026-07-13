@props(['disabled' => false])

<input
    @disabled($disabled)
    {{ $attributes->merge([
        'class' => 'w-full rounded-lg border border-outline-variant px-4 py-3 font-body-md text-sm text-on-surface
                    focus:outline-none focus:ring-2 focus:ring-primary focus:border-primary
                    disabled:bg-surface-container disabled:cursor-not-allowed'
    ]) }}
>