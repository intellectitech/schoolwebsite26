<x-layouts.admin :title="'Add Testimonial'" :active="'testimonials'">

    <x-admin.page-header title="Add Testimonial" />

    <form method="POST" action="{{ route('admin.testimonials.store') }}" enctype="multipart/form-data" class="bg-white rounded-xl border border-outline-variant/20 p-6 max-w-2xl">
        @csrf
        @include('admin.testimonials._form')

        <div class="flex justify-end gap-3 mt-6 pt-6 border-t border-outline-variant/20">
            <x-button variant="outline" href="{{ route('admin.testimonials.index') }}">Cancel</x-button>
            <x-button type="submit" variant="primary">Add Testimonial</x-button>
        </div>
    </form>

</x-layouts.admin>