@php $item = $item ?? null; @endphp

<div class="grid grid-cols-1 gap-5">
    <x-admin.form-field name="title" label="Title" required :value="$item?->title" placeholder="e.g. Athletics" />
    <x-admin.form-field name="tagline" label="Tagline" required :value="$item?->tagline" placeholder="e.g. Champions of Art and Grace" />

    <div>
        <x-input-label for="image" :value="'Image' . ($item ? '' : ' *')" />

        @if($item?->image_url)
            <img src="{{ $item->image_url }}" alt="" class="w-32 h-32 rounded-lg object-cover mb-2">
        @endif

        <input
            type="file" id="image" name="image" accept="image/*"
            class="w-full text-sm text-on-surface-variant file:mr-4 file:py-2.5 file:px-4 file:rounded-lg file:border-0 file:bg-primary/5 file:text-primary file:font-label-md file:text-sm hover:file:bg-primary/10"
        >
        <x-input-error :messages="$errors->get('image')" class="mt-1" />
    </div>

    <x-admin.form-field name="sort_order" label="Display Order" type="number" :value="$item?->sort_order ?? 0" />
</div>