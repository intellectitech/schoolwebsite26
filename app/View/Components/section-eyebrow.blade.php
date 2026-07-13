@props(['text'])

<p {{ $attributes->merge(['class' => 'font-label-md text-label-md text-secondary uppercase tracking-wider mb-2']) }}>
    {{ $text }}
</p>