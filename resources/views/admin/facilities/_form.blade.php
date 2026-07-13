@php $facility = $facility ?? null; @endphp

<div class="grid grid-cols-1 gap-5">
    <x-admin.form-field name="name" label="Facility Name" required :value="$facility?->name" placeholder="e.g. The Heritage Library" />

    <x-admin.form-field name="description" label="Description (optional)" type="textarea" :rows="2" :value="$facility?->description" />

    <div>
        <x-input-label for="image" :value="'Image' . ($facility ? '' : ' *')" />

        @if($facility?->image_url)
            <img src="{{ $facility->image_url }}" alt="" class="w-32 h-32 rounded-lg object-cover mb-2">
        @endif

        <input
            type="file" id="image" name="image" accept="image/*"
            class="w-full text-sm text-on-surface-variant file:mr-4 file:py-2.5 file:px-4 file:rounded-lg file:border-0 file:bg-primary/5 file:text-primary file:font-label-md file:text-sm hover:file:bg-primary/10"
        >
        <x-input-error :messages="$errors->get('image')" class="mt-1" />
    </div>

    <x-admin.form-field name="sort_order" label="Display Order" type="number" :value="$facility?->sort_order ?? 0" />

    <div class="flex items-center gap-3">
        <input
            type="checkbox" id="is_featured" name="is_featured" value="1"
            @checked(old('is_featured', $facility?->is_featured))
            class="w-5 h-5 rounded border-outline-variant text-primary focus:ring-primary"
        >
        <x-input-label for="is_featured" value="Feature as the large facility card on the Campus page" class="!mb-0" />
    </div>

    <p class="text-xs text-on-surface-variant -mt-3">
        Only one facility should be featured at a time — it renders as the large image on the Campus page, with the rest shown as smaller stacked cards.
    </p>
</div>