<x-layouts.admin :title="'Add Pathway'" :active="'pathways'">

    <x-admin.page-header title="Add Pathway" />

    <form method="POST" action="{{ route('admin.pathways.store') }}" enctype="multipart/form-data" class="bg-white rounded-xl border border-outline-variant/20 p-6 max-w-xl">
        @csrf
        @include('admin.pathways._form')

        <div class="flex justify-end gap-3 mt-6 pt-6 border-t border-outline-variant/20">
            <x-button variant="outline" href="{{ route('admin.pathways.index') }}">Cancel</x-button>
            <x-button type="submit" variant="primary">Add Pathway</x-button>
        </div>
    </form>

</x-layouts.admin>