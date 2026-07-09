<x-layouts.admin :title="'Testimonials'" :active="'testimonials'">

    <x-admin.flash-message />
    <x-admin.page-header title="Student Stories" create-route="admin.testimonials.create" create-label="Add Testimonial" />

    <x-admin.data-table>
        <thead class="bg-surface-container-low">
            <tr class="text-left">
                <th class="px-5 py-3 font-label-md text-label-md text-on-surface-variant">Student</th>
                <th class="px-5 py-3 font-label-md text-label-md text-on-surface-variant">Quote</th>
                <th class="px-5 py-3"></th>
            </tr>
        </thead>
        <tbody class="divide-y divide-outline-variant/10">
            @forelse($testimonials as $testimonial)
                <tr class="hover:bg-surface-container-low/50 transition-colors">
                    <td class="px-5 py-3">
                        <div class="flex items-center gap-3">
                            @if($testimonial->photo_url)
                                <img src="{{ $testimonial->photo_url }}" alt="" class="w-10 h-10 rounded-full object-cover flex-shrink-0">
                            @else
                                <div class="w-10 h-10 rounded-full bg-primary/10 flex items-center justify-center text-primary text-sm font-semibold flex-shrink-0">
                                    {{ strtoupper(substr($testimonial->student_name, 0, 1)) }}
                                </div>
                            @endif
                            <div>
                                <p class="font-body-md text-sm font-semibold text-on-surface">{{ $testimonial->student_name }}</p>
                                <p class="text-xs text-on-surface-variant">{{ $testimonial->student_class }}</p>
                            </div>
                        </div>
                    </td>
                    <td class="px-5 py-3 text-sm text-on-surface-variant max-w-md truncate">{{ $testimonial->quote }}</td>
                    <td class="px-5 py-3">
                        <div class="flex items-center justify-end gap-3">
                            <a href="{{ route('admin.testimonials.edit', $testimonial) }}" class="text-primary hover:text-primary-container" title="Edit">
                                <x-icon name="edit" class="text-xl" />
                            </a>
                            <x-admin.delete-form :action="route('admin.testimonials.destroy', $testimonial)" />
                        </div>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="3" class="px-5 py-10 text-center text-sm text-on-surface-variant">No testimonials yet.</td>
                </tr>
            @endforelse
        </tbody>
    </x-admin.data-table>

    <div class="mt-6">{{ $testimonials->links() }}</div>

</x-layouts.admin>