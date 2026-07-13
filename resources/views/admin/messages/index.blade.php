<x-layouts.admin :title="'Messages'" :active="'messages'">

    <x-admin.flash-message />
    <x-admin.page-header title="Contact Messages" />

    <x-admin.data-table empty-message="No messages yet.">
        <thead class="bg-surface-container-low">
            <tr class="text-left">
                <th class="px-5 py-3 font-label-md text-label-md text-on-surface-variant"></th>
                <th class="px-5 py-3 font-label-md text-label-md text-on-surface-variant">From</th>
                <th class="px-5 py-3 font-label-md text-label-md text-on-surface-variant">Subject</th>
                <th class="px-5 py-3 font-label-md text-label-md text-on-surface-variant">Received</th>
                <th class="px-5 py-3"></th>
            </tr>
        </thead>
        <tbody class="divide-y divide-outline-variant/10">
            @forelse($messages as $message)
                <tr class="hover:bg-surface-container-low/50 transition-colors {{ ! $message->is_read ? 'font-semibold' : '' }}">
                    <td class="px-5 py-3">
                        @unless($message->is_read)
                            <span class="w-2 h-2 rounded-full bg-primary block" title="Unread"></span>
                        @endunless
                    </td>
                    <td class="px-5 py-3">
                        <p class="text-sm text-on-surface">{{ $message->full_name }}</p>
                        <p class="text-xs text-on-surface-variant font-normal">{{ $message->email }}</p>
                    </td>
                    <td class="px-5 py-3 text-sm text-on-surface-variant">{{ $message->subject ?? '—' }}</td>
                    <td class="px-5 py-3 text-sm text-on-surface-variant font-normal">{{ $message->created_at->diffForHumans() }}</td>
                    <td class="px-5 py-3">
                        <div class="flex items-center justify-end gap-3">
                            <a href="{{ route('admin.messages.show', $message) }}" class="text-primary hover:text-primary-container font-label-md text-label-md">
                                View
                            </a>
                            <x-admin.delete-form :action="route('admin.messages.destroy', $message)" />
                        </div>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="5" class="px-5 py-10 text-center text-sm text-on-surface-variant">No messages yet.</td>
                </tr>
            @endforelse
        </tbody>
    </x-admin.data-table>

    <div class="mt-6">{{ $messages->links() }}</div>

</x-layouts.admin>