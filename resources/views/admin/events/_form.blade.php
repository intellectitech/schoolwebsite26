@php $event = $event ?? null; @endphp

<div class="grid grid-cols-1 md:grid-cols-2 gap-5">
    <div class="md:col-span-2">
        <x-admin.form-field name="title" label="Event Title" required :value="$event?->title" />
    </div>

    <x-admin.form-field
        name="event_date" label="Date" type="date" required
        :value="$event?->event_date?->format('Y-m-d')"
    />
    <x-admin.form-field
        name="event_time" label="Time (optional)" type="time"
        :value="$event?->event_time?->format('H:i')"
    />

    <div class="md:col-span-2">
        <x-admin.form-field name="location" label="Location" :value="$event?->location" placeholder="e.g. Auden Auditorium" />
    </div>

    <div class="md:col-span-2">
        <x-admin.form-field name="description" label="Description (optional)" type="textarea" :rows="3" :value="$event?->description" />
    </div>
</div>