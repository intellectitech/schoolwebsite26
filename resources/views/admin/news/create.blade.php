<x-layouts.admin :title="'Add News Post'" :active="'news'">

    <x-admin.page-header title="Add News Post" />

    <form method="POST" action="{{ route('admin.news.store') }}" enctype="multipart/form-data" class="bg-white rounded-xl border border-outline-variant/20 p-6 max-w-3xl">
        @csrf
        @include('admin.news._form')

        <div class="flex justify-end gap-3 mt-6 pt-6 border-t border-outline-variant/20">
            <x-button variant="outline" href="{{ route('admin.news.index') }}">Cancel</x-button>
            <x-button type="submit" variant="primary">Create Post</x-button>
        </div>
    </form>

</x-layouts.admin>