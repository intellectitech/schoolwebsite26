<x-layouts.admin :title="'Edit Gallery Item'" :active="'gallery'">

    <x-admin.page-header title="Edit Gallery Item" />

    <form method="POST" action="{{ route('admin.gallery.update', $item) }}" enctype="multipart/form-data" class="bg-white rounded-xl border border-outline-variant/20 p-6 max-w-xl">
        @csrf
        @method('PUT')
        @include('admin.gallery._form')

        <div class="flex justify-end gap-3 mt-6 pt-6 border-t border-outline-variant/20">
            <x-button variant="outline" href="{{ route('admin.gallery.index') }}">Cancel</x-button>
            <x-button type="submit" variant="primary">Save Changes</x-button>
        </div>
    </form>

</x-layouts.admin>