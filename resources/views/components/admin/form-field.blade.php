@props(['name', 'label', 'type' => 'text', 'value' => null, 'required' => false, 'rows' => null])

<div>
    <x-input-label :for="$name" :value="$label . ($required ? ' *' : '')" />

    @if($type === 'textarea')
        <textarea
            id="{{ $name }}"
            name="{{ $name }}"
            rows="{{ $rows ?? 4 }}"
            @required($required)
            {{ $attributes->merge(['class' => 'w-full rounded-lg border border-outline-variant px-4 py-3 font-body-md text-sm text-on-surface focus:outline-none focus:ring-2 focus:ring-primary focus:border-primary']) }}
        >{{ old($name, $value) }}</textarea>
    @else
        <input
            id="{{ $name }}"
            type="{{ $type }}"
            name="{{ $name }}"
            value="{{ old($name, $value) }}"
            @required($required)
            {{ $attributes->merge([
                'class' => 'w-full rounded-lg border border-outline-variant px-4 py-3 font-body-md text-sm text-on-surface
                    focus:outline-none focus:ring-2 focus:ring-primary focus:border-primary
                    disabled:bg-surface-container disabled:cursor-not-allowed'
            ]) }}
        />
    @endif

    <x-input-error :messages="$errors->get($name)" class="mt-1" />
</div>
