<x-layouts.admin :title="'Add FAQ'" :active="'faqs'">

    <x-admin.page-header title="Add FAQ" />

    <form method="POST" action="{{ route('admin.faqs.store') }}" class="bg-white rounded-xl border border-outline-variant/20 p-6 max-w-2xl">
        @csrf
        @include('admin.faqs._form')

        <div class="flex justify-end gap-3 mt-6 pt-6 border-t border-outline-variant/20">
            <x-button variant="outline" href="{{ route('admin.faqs.index') }}">Cancel</x-button>
            <x-button type="submit" variant="primary">Add FAQ</x-button>
        </div>
    </form>

</x-layouts.admin>