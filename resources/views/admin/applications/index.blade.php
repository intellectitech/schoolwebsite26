<x-layouts.admin :title="'Applications'" :active="'applications'">

    <x-admin.flash-message />
    <x-admin.page-header title="Submitted Applications" />

    <x-admin.data-table empty-message="No applications submitted yet.">
        <thead class="bg-surface-container-low">
            <tr class="text-left">
                <th class="px-5 py-3 font-label-md text-label-md text-on-surface-variant">Applicant</th>
                <th class="px-5 py-3 font-label-md text-label-md text-on-surface-variant">Grade</th>
                <th class="px-5 py-3 font-label-md text-label-md text-on-surface-variant">Submitted</th>
                <th class="px-5 py-3 font-label-md text-label-md text-on-surface-variant">Status</th>
                <th class="px-5 py-3"></th>
            </tr>
        </thead>
        <tbody class="divide-y divide-outline-variant/10">
            @forelse($applications as $application)
                <tr class="hover:bg-surface-container-low/50 transition-colors">
                    <td class="px-5 py-3">
                        <p class="font-body-md text-sm font-semibold text-on-surface">{{ $application->full_name }}</p>
                        <p class="text-xs text-on-surface-variant">{{ $application->email }}</p>
                    </td>
                    <td class="px-5 py-3 text-sm text-on-surface-variant">{{ $application->grade_applying_for }}</td>
                    <td class="px-5 py-3 text-sm text-on-surface-variant">{{ $application->created_at->format('M j, Y') }}</td>
                    <td class="px-5 py-3">
                        @php
                            $statusColors = [
                                'pending' => 'bg-surface-container text-on-surface-variant',
                                'reviewed' => 'bg-blue-100 text-blue-700',
                                'accepted' => 'bg-green-100 text-green-700',
                                'rejected' => 'bg-error-container text-on-error-container',
                            ];
                        @endphp
                        <span class="text-xs font-semibold px-2 py-1 rounded-full capitalize {{ $statusColors[$application->status] }}">
                            {{ $application->status }}
                        </span>
                    </td>
                    <td class="px-5 py-3">
                        <a href="{{ route('admin.applications.show', $application) }}" class="text-primary hover:text-primary-container font-label-md text-label-md">
                            Review
                        </a>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="5" class="px-5 py-10 text-center text-sm text-on-surface-variant">No applications submitted yet.</td>
                </tr>
            @endforelse
        </tbody>
    </x-admin.data-table>

    <div class="mt-6">{{ $applications->links() }}</div>

</x-layouts.admin>