<x-layouts.admin :title="'Edit Admission Step'" :active="'admission-steps'">

    <x-admin.page-header title="Edit Admission Step" />

    <form method="POST" action="{{ route('admin.admission-steps.update', $step) }}" class="bg-white rounded-xl border border-outline-variant/20 p-6 max-w-2xl">
        @csrf
        @method('PUT')
        @include('admin.admission-steps._form')

        <div class="flex justify-end gap-3 mt-6 pt-6 border-t border-outline-variant/20">
            <x-button variant="outline" href="{{ route('admin.admission-steps.index') }}">Cancel</x-button>
            <x-button type="submit" variant="primary">Save Changes</x-button>
        </div>
    </form>

</x-layouts.admin>