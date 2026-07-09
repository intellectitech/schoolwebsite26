@php $step = $step ?? null; @endphp

<div class="grid grid-cols-1 md:grid-cols-2 gap-5">
    <x-admin.form-field name="step_number" label="Step Number" type="number" required :value="$step?->step_number" />
    <x-admin.form-field name="title" label="Title" required :value="$step?->title" placeholder="e.g. Inquiry" />

    <div class="md:col-span-2">
        <x-admin.form-field name="description" label="Description" type="textarea" :rows="2" required :value="$step?->description" />
    </div>

    <div class="md:col-span-2">
        <x-admin.form-field name="icon" label="Icon Name" required :value="$step?->icon" placeholder="e.g. edit_note" />
        <x-admin.icon-picker-hint />
    </div>

    <x-admin.form-field name="sort_order" label="Display Order" type="number" :value="$step?->sort_order ?? 0" />
</div>