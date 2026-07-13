<x-layouts.admin :title="'Events'" :active="'events'">

    <x-admin.flash-message />
    <x-admin.page-header title="Events" create-route="admin.events.create" create-label="Add Event" />

    <x-admin.data-table empty-message="No events yet.">
        <thead class="bg-surface-container-low">
            <tr class="text-left">
                <th class="px-5 py-3 font-label-md text-label-md text-on-surface-variant">Event</th>
                <th class="px-5 py-3 font-label-md text-label-md text-on-surface-variant">Date & Time</th>
                <th class="px-5 py-3 font-label-md text-label-md text-on-surface-variant">Location</th>
                <th class="px-5 py-3"></th>
            </tr>
        </thead>
        <tbody class="divide-y divide-outline-variant/10">
            @forelse($events as $event)
                <tr class="hover:bg-surface-container-low/50 transition-colors">
                    <td class="px-5 py-3">
                        <p class="font-body-md text-sm font-semibold text-on-surface">{{ $event->title }}</p>
                        @if($event->event_date->isPast())
                            <span class="text-[10px] text-on-surface-variant">Past event</span>
                        @endif
                    </td>
                    <td class="px-5 py-3 text-sm text-on-surface-variant">
                        {{ $event->event_date->format('M j, Y') }}
                        @if($event->event_time) &middot; {{ $event->event_time->format('g:i A') }} @endif
                    </td>
                    <td class="px-5 py-3 text-sm text-on-surface-variant">{{ $event->location ?? '—' }}</td>
                    <td class="px-5 py-3">
                        <div class="flex items-center justify-end gap-3">
                            <a href="{{ route('admin.events.edit', $event) }}" class="text-primary hover:text-primary-container" title="Edit">
                                <x-icon name="edit" class="text-xl" />
                            </a>
                            <x-admin.delete-form :action="route('admin.events.destroy', $event)" />
                        </div>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="4" class="px-5 py-10 text-center text-sm text-on-surface-variant">No events yet.</td>
                </tr>
            @endforelse
        </tbody>
    </x-admin.data-table>

    <div class="mt-6">{{ $events->links() }}</div>

</x-layouts.admin>