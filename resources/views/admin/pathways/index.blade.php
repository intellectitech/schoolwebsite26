<x-layouts.admin :title="'Pathways'" :active="'pathways'">

    <x-admin.flash-message />
    <x-admin.page-header title="Educational Pathways" create-route="admin.pathways.create" create-label="Add Pathway" />

    <x-admin.data-table>
        <thead class="bg-surface-container-low">
            <tr class="text-left">
                <th class="px-5 py-3 font-label-md text-label-md text-on-surface-variant">Pathway</th>
                <th class="px-5 py-3 font-label-md text-label-md text-on-surface-variant">Description</th>
                <th class="px-5 py-3"></th>
            </tr>
        </thead>
        <tbody class="divide-y divide-outline-variant/10">
            @forelse($pathways as $pathway)
                <tr class="hover:bg-surface-container-low/50 transition-colors">
                    <td class="px-5 py-3">
                        <div class="flex items-center gap-3">
                            <img src="{{ $pathway->image_url }}" alt="" class="w-14 h-14 rounded-lg object-cover flex-shrink-0">
                            <p class="font-body-md text-sm font-semibold text-on-surface">{{ $pathway->title }}</p>
                        </div>
                    </td>
                    <td class="px-5 py-3 text-sm text-on-surface-variant max-w-sm truncate">{{ $pathway->description }}</td>
                    <td class="px-5 py-3">
                        <div class="flex items-center justify-end gap-3">
                            <a href="{{ route('admin.pathways.edit', $pathway) }}" class="text-primary hover:text-primary-container" title="Edit">
                                <x-icon name="edit" class="text-xl" />
                            </a>
                            <x-admin.delete-form :action="route('admin.pathways.destroy', $pathway)" />
                        </div>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="3" class="px-5 py-10 text-center text-sm text-on-surface-variant">No pathways yet.</td>
                </tr>
            @endforelse
        </tbody>
    </x-admin.data-table>

</x-layouts.admin>