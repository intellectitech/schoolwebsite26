<x-layouts.admin :title="'Add Gallery Item'" :active="'gallery'">

    <x-admin.page-header title="Add Gallery Item" />

    <form method="POST" action="{{ route('admin.gallery.store') }}" enctype="multipart/form-data" class="bg-white rounded-xl border border-outline-variant/20 p-6 max-w-xl">
        @csrf
        @include('admin.gallery._form')

        <div class="flex justify-end gap-3 mt-6 pt-6 border-t border-outline-variant/20">
            <x-button variant="outline" href="{{ route('admin.gallery.index') }}">Cancel</x-button>
            <x-button type="submit" variant="primary">Add Item</x-button>
        </div>
    </form>

</x-layouts.admin>