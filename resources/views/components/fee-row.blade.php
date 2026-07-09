@props(['label', 'amount'])

<div class="flex justify-between items-center py-3 border-b border-outline-variant/20 last:border-0">
    <span class="font-body-md text-sm text-on-surface-variant">{{ $label }}</span>
    <span class="font-body-lg text-body-lg !text-base font-semibold text-on-surface">{{ $amount }}</span>
</div>