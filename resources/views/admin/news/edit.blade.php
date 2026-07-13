<x-layouts.admin :title="'Edit News Post'" :active="'news'">

    <x-admin.page-header title="Edit News Post" />

    <form method="POST" action="{{ route('admin.news.update', $post) }}" enctype="multipart/form-data" class="bg-white rounded-xl border border-outline-variant/20 p-6 max-w-3xl">
        @csrf
        @method('PUT')
        @include('admin.news._form')

        <div class="flex justify-end gap-3 mt-6 pt-6 border-t border-outline-variant/20">
            <x-button variant="outline" href="{{ route('admin.news.index') }}">Cancel</x-button>
            <x-button type="submit" variant="primary">Save Changes</x-button>
        </div>
    </form>

</x-layouts.admin>