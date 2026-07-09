<x-layouts.admin :title="'Facilities'" :active="'gallery'">

    <x-admin.flash-message />
    <x-admin.page-header title="World-Class Facilities" create-route="admin.facilities.create" create-label="Add Facility" />

    <x-admin.data-table>
        <thead class="bg-surface-container-low">
            <tr class="text-left">
                <th class="px-5 py-3 font-label-md text-label-md text-on-surface-variant">Facility</th>
                <th class="px-5 py-3 font-label-md text-label-md text-on-surface-variant">Featured</th>
                <th class="px-5 py-3"></th>
            </tr>
        </thead>
        <tbody class="divide-y divide-outline-variant/10">
            @forelse($facilities as $facility)
                <tr class="hover:bg-surface-container-low/50 transition-colors">
                    <td class="px-5 py-3">
                        <div class="flex items-center gap-3">
                            <img src="{{ $facility->image_url }}" alt="" class="w-14 h-14 rounded-lg object-cover flex-shrink-0">
                            <div>
                                <p class="font-body-md text-sm font-semibold text-on-surface">{{ $facility->name }}</p>
                                <p class="text-xs text-on-surface-variant truncate max-w-xs">{{ $facility->description }}</p>
                            </div>
                        </div>
                    </td>
                    <td class="px-5 py-3">
                        @if($facility->is_featured)
                            <span class="text-xs font-semibold text-green-700 bg-green-100 px-2 py-1 rounded-full">Featured</span>
                        @else
                            <span class="text-xs font-semibold text-on-surface-variant bg-surface-container px-2 py-1 rounded-full">Standard</span>
                        @endif
                    </td>
                    <td class="px-5 py-3">
                        <div class="flex items-center justify-end gap-3">
                            <a href="{{ route('admin.facilities.edit', $facility) }}" class="text-primary hover:text-primary-container" title="Edit">
                                <x-icon name="edit" class="text-xl" />
                            </a>
                            <x-admin.delete-form :action="route('admin.facilities.destroy', $facility)" />
                        </div>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="3" class="px-5 py-10 text-center text-sm text-on-surface-variant">No facilities yet.</td>
                </tr>
            @endforelse
        </tbody>
    </x-admin.data-table>

    <div class="mt-6">{{ $facilities->links() }}</div>

</x-layouts.admin>