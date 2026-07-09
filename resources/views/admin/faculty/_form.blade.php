@php $faculty = $faculty ?? null; @endphp

<div class="grid grid-cols-1 md:grid-cols-2 gap-5">
    <x-admin.form-field name="name" label="Full Name" required :value="$faculty?->name" placeholder="e.g. Dr. Eleanor Vance" />
    <x-admin.form-field name="title" label="Title / Role" required :value="$faculty?->title" placeholder="e.g. Dean of Humanities" />

    <div class="md:col-span-2">
        <x-admin.form-field name="bio" label="Short Bio (optional)" type="textarea" :rows="2" :value="$faculty?->bio" />
    </div>

    <div class="md:col-span-2">
        <x-input-label for="photo" :value="'Photo' . ($faculty ? '' : ' *')" />

        @if($faculty?->photo_url)
            <img src="{{ $faculty->photo_url }}" alt="" class="w-24 h-24 rounded-full object-cover mb-2">
            <p class="text-xs text-on-surface-variant mb-2">Current photo — upload a new file below to replace it.</p>
        @endif

        <input
            type="file" id="photo" name="photo" accept="image/*"
            class="w-full text-sm text-on-surface-variant file:mr-4 file:py-2.5 file:px-4 file:rounded-lg file:border-0 file:bg-primary/5 file:text-primary file:font-label-md file:text-sm hover:file:bg-primary/10"
        >
        <x-input-error :messages="$errors->get('photo')" class="mt-1" />
    </div>

    <x-admin.form-field name="sort_order" label="Display Order" type="number" :value="$faculty?->sort_order ?? 0" />

    <div class="flex items-center gap-3 pt-8">
        <input
            type="checkbox" id="is_spotlighted" name="is_spotlighted" value="1"
            @checked(old('is_spotlighted', $faculty?->is_spotlighted ?? true))
            class="w-5 h-5 rounded border-outline-variant text-primary focus:ring-primary"
        >
        <x-input-label for="is_spotlighted" value="Show on Academics page" class="!mb-0" />
    </div>
</div>