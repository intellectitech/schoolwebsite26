<x-layouts.admin :title="'Campus Gallery'" :active="'gallery'">

    <x-admin.flash-message />
    <x-admin.page-header title="Student Life Gallery" create-route="admin.gallery.create" create-label="Add Gallery Item" />

    <x-admin.data-table>
        <thead class="bg-surface-container-low">
            <tr class="text-left">
                <th class="px-5 py-3 font-label-md text-label-md text-on-surface-variant">Item</th>
                <th class="px-5 py-3 font-label-md text-label-md text-on-surface-variant">Tagline</th>
                <th class="px-5 py-3"></th>
            </tr>
        </thead>
        <tbody class="divide-y divide-outline-variant/10">
            @forelse($galleryItems as $item)
                <tr class="hover:bg-surface-container-low/50 transition-colors">
                    <td class="px-5 py-3">
                        <div class="flex items-center gap-3">
                            <img src="{{ $item->image_url }}" alt="" class="w-14 h-14 rounded-lg object-cover flex-shrink-0">
                            <p class="font-body-md text-sm font-semibold text-on-surface">{{ $item->title }}</p>
                        </div>
                    </td>
                    <td class="px-5 py-3 text-sm text-on-surface-variant">{{ $item->tagline }}</td>
                    <td class="px-5 py-3">
                        <div class="flex items-center justify-end gap-3">
                            <a href="{{ route('admin.gallery.edit', $item) }}" class="text-primary hover:text-primary-container" title="Edit">
                                <x-icon name="edit" class="text-xl" />
                            </a>
                            <x-admin.delete-form :action="route('admin.gallery.destroy', $item)" />
                        </div>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="3" class="px-5 py-10 text-center text-sm text-on-surface-variant">No gallery items yet.</td>
                </tr>
            @endforelse
        </tbody>
    </x-admin.data-table>

    <div class="mt-6">{{ $galleryItems->links() }}</div>

</x-layouts.admin>