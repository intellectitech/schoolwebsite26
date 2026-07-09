<x-layouts.admin :title="'Admission Steps'" :active="'admission-steps'">

    <x-admin.flash-message />
    <x-admin.page-header title="Admission Process Steps" create-route="admin.admission-steps.create" create-label="Add Step" />

    <x-admin.data-table>
        <thead class="bg-surface-container-low">
            <tr class="text-left">
                <th class="px-5 py-3 font-label-md text-label-md text-on-surface-variant">Step</th>
                <th class="px-5 py-3 font-label-md text-label-md text-on-surface-variant">Description</th>
                <th class="px-5 py-3 font-label-md text-label-md text-on-surface-variant">Icon</th>
                <th class="px-5 py-3"></th>
            </tr>
        </thead>
        <tbody class="divide-y divide-outline-variant/10">
            @forelse($steps as $step)
                <tr class="hover:bg-surface-container-low/50 transition-colors">
                    <td class="px-5 py-3">
                        <div class="flex items-center gap-3">
                            <div class="w-8 h-8 rounded-full bg-primary/5 flex items-center justify-center text-primary text-xs font-bold flex-shrink-0">
                                {{ $step->step_number }}
                            </div>
                            <p class="font-body-md text-sm font-semibold text-on-surface">{{ $step->title }}</p>
                        </div>
                    </td>
                    <td class="px-5 py-3 text-sm text-on-surface-variant max-w-sm truncate">{{ $step->description }}</td>
                    <td class="px-5 py-3">
                        <div class="flex items-center gap-2 text-on-surface-variant">
                            <x-icon :name="$step->icon" class="text-lg" />
                            <code class="text-xs">{{ $step->icon }}</code>
                        </div>
                    </td>
                    <td class="px-5 py-3">
                        <div class="flex items-center justify-end gap-3">
                            <a href="{{ route('admin.admission-steps.edit', $step) }}" class="text-primary hover:text-primary-container" title="Edit">
                                <x-icon name="edit" class="text-xl" />
                            </a>
                            <x-admin.delete-form :action="route('admin.admission-steps.destroy', $step)" />
                        </div>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="4" class="px-5 py-10 text-center text-sm text-on-surface-variant">No admission steps yet.</td>
                </tr>
            @endforelse
        </tbody>
    </x-admin.data-table>

</x-layouts.admin>