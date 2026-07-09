@php $testimonial = $testimonial ?? null; @endphp

<div class="grid grid-cols-1 md:grid-cols-2 gap-5">
    <x-admin.form-field name="student_name" label="Student Name" required :value="$testimonial?->student_name" />
    <x-admin.form-field name="student_class" label="Class / Program" required :value="$testimonial?->student_class" placeholder="e.g. Class of 2024 - Biomedical Engineering" />

    <div class="md:col-span-2">
        <x-admin.form-field name="quote" label="Quote" type="textarea" :rows="4" required :value="$testimonial?->quote" />
    </div>

    <div class="md:col-span-2">
        <x-input-label for="photo" value="Photo (optional — an initial avatar is shown if left blank)" />

        @if($testimonial?->photo_url)
            <img src="{{ $testimonial->photo_url }}" alt="" class="w-16 h-16 rounded-full object-cover mb-2">
        @endif

        <input
            type="file" id="photo" name="photo" accept="image/*"
            class="w-full text-sm text-on-surface-variant file:mr-4 file:py-2.5 file:px-4 file:rounded-lg file:border-0 file:bg-primary/5 file:text-primary file:font-label-md file:text-sm hover:file:bg-primary/10"
        >
        <x-input-error :messages="$errors->get('photo')" class="mt-1" />
    </div>

    <x-admin.form-field name="sort_order" label="Display Order" type="number" :value="$testimonial?->sort_order ?? 0" />
</div>