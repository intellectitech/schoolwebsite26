<x-layouts.admin :title="'Add Event'" :active="'events'">

    <x-admin.page-header title="Add Event" />

    <form method="POST" action="{{ route('admin.events.store') }}" class="bg-white rounded-xl border border-outline-variant/20 p-6 max-w-2xl">
        @csrf
        @include('admin.events._form')

        <div class="flex justify-end gap-3 mt-6 pt-6 border-t border-outline-variant/20">
            <x-button variant="outline" href="{{ route('admin.events.index') }}">Cancel</x-button>
            <x-button type="submit" variant="primary">Create Event</x-button>
        </div>
    </form>

</x-layouts.admin>