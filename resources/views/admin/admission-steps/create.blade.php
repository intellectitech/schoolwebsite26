<x-layouts.admin :title="'Add Admission Step'" :active="'admission-steps'">

    <x-admin.page-header title="Add Admission Step" />

    <form method="POST" action="{{ route('admin.admission-steps.store') }}" class="bg-white rounded-xl border border-outline-variant/20 p-6 max-w-2xl">
        @csrf
        @include('admin.admission-steps._form')

        <div class="flex justify-end gap-3 mt-6 pt-6 border-t border-outline-variant/20">
            <x-button variant="outline" href="{{ route('admin.admission-steps.index') }}">Cancel</x-button>
            <x-button type="submit" variant="primary">Add Step</x-button>
        </div>
    </form>

</x-layouts.admin>