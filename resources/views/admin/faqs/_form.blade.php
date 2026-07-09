@php $faq = $faq ?? null; @endphp

<div class="grid grid-cols-1 gap-5">
    <x-admin.form-field name="question" label="Question" required :value="$faq?->question" />
    <x-admin.form-field name="answer" label="Answer" type="textarea" :rows="4" required :value="$faq?->answer" />
    <x-admin.form-field
        name="category" label="Category (optional — defaults to 'admissions')"
        :value="$faq?->category" placeholder="e.g. admissions, campus"
    />
    <x-admin.form-field name="sort_order" label="Display Order" type="number" :value="$faq?->sort_order ?? 0" />
</div>