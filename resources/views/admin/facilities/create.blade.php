<x-layouts.admin :title="'Add Facility'" :active="'gallery'">

    <x-admin.page-header title="Add Facility" />

    <form method="POST" action="{{ route('admin.facilities.store') }}" enctype="multipart/form-data" class="bg-white rounded-xl border border-outline-variant/20 p-6 max-w-xl">
        @csrf
        @include('admin.facilities._form')

        <div class="flex justify-end gap-3 mt-6 pt-6 border-t border-outline-variant/20">
            <x-button variant="outline" href="{{ route('admin.facilities.index') }}">Cancel</x-button>
            <x-button type="submit" variant="primary">Add Facility</x-button>
        </div>
    </form>

</x-layouts.admin>