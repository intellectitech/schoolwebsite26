{{-- Usage: <x-icon name="menu" class="text-primary text-2xl" :filled="false" /> --}}
@props(['name', 'filled' => false])

<span
    {{ $attributes->merge(['class' => 'material-symbols-outlined']) }}
    @if($filled) style="font-variation-settings: 'FILL' 1;" @endif
>{{ $name }}</span>