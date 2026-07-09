<x-layouts.admin :title="'Faculty'" :active="'faculty'">

    <x-admin.flash-message />
    <x-admin.page-header title="Faculty Spotlight" create-route="admin.faculty.create" create-label="Add Faculty Member" />

    <x-admin.data-table>
        <thead class="bg-surface-container-low">
            <tr class="text-left">
                <th class="px-5 py-3 font-label-md text-label-md text-on-surface-variant">Name</th>
                <th class="px-5 py-3 font-label-md text-label-md text-on-surface-variant">Title</th>
                <th class="px-5 py-3 font-label-md text-label-md text-on-surface-variant">Spotlighted</th>
                <th class="px-5 py-3"></th>
            </tr>
        </thead>
        <tbody class="divide-y divide-outline-variant/10">
            @forelse($faculty as $member)
                <tr class="hover:bg-surface-container-low/50 transition-colors">
                    <td class="px-5 py-3">
                        <div class="flex items-center gap-3">
                            <img src="{{ $member->photo_url }}" alt="" class="w-10 h-10 rounded-full object-cover flex-shrink-0">
                            <p class="font-body-md text-sm font-semibold text-on-surface">{{ $member->name }}</p>
                        </div>
                    </td>
                    <td class="px-5 py-3 text-sm text-on-surface-variant">{{ $member->title }}</td>
                    <td class="px-5 py-3">
                        @if($member->is_spotlighted)
                            <span class="text-xs font-semibold text-green-700 bg-green-100 px-2 py-1 rounded-full">Yes</span>
                        @else
                            <span class="text-xs font-semibold text-on-surface-variant bg-surface-container px-2 py-1 rounded-full">No</span>
                        @endif
                    </td>
                    <td class="px-5 py-3">
                        <div class="flex items-center justify-end gap-3">
                            <a href="{{ route('admin.faculty.edit', $member) }}" class="text-primary hover:text-primary-container" title="Edit">
                                <x-icon name="edit" class="text-xl" />
                            </a>
                            <x-admin.delete-form :action="route('admin.faculty.destroy', $member)" />
                        </div>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="4" class="px-5 py-10 text-center text-sm text-on-surface-variant">No faculty members yet.</td>
                </tr>
            @endforelse
        </tbody>
    </x-admin.data-table>

    <div class="mt-6">{{ $faculty->links() }}</div>

</x-layouts.admin>