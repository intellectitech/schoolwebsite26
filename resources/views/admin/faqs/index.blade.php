<x-layouts.admin :title="'FAQs'" :active="'faqs'">

    <x-admin.flash-message />
    <x-admin.page-header title="Frequently Asked Questions" create-route="admin.faqs.create" create-label="Add FAQ" />

    <x-admin.data-table>
        <thead class="bg-surface-container-low">
            <tr class="text-left">
                <th class="px-5 py-3 font-label-md text-label-md text-on-surface-variant">Question</th>
                <th class="px-5 py-3 font-label-md text-label-md text-on-surface-variant">Category</th>
                <th class="px-5 py-3"></th>
            </tr>
        </thead>
        <tbody class="divide-y divide-outline-variant/10">
            @forelse($faqs as $faq)
                <tr class="hover:bg-surface-container-low/50 transition-colors">
                    <td class="px-5 py-3">
                        <p class="font-body-md text-sm font-semibold text-on-surface">{{ $faq->question }}</p>
                        <p class="text-xs text-on-surface-variant truncate max-w-md">{{ $faq->answer }}</p>
                    </td>
                    <td class="px-5 py-3">
                        <span class="text-xs font-semibold text-primary bg-primary/5 px-2 py-1 rounded-full capitalize">{{ $faq->category }}</span>
                    </td>
                    <td class="px-5 py-3">
                        <div class="flex items-center justify-end gap-3">
                            <a href="{{ route('admin.faqs.edit', $faq) }}" class="text-primary hover:text-primary-container" title="Edit">
                                <x-icon name="edit" class="text-xl" />
                            </a>
                            <x-admin.delete-form :action="route('admin.faqs.destroy', $faq)" />
                        </div>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="3" class="px-5 py-10 text-center text-sm text-on-surface-variant">No FAQs yet.</td>
                </tr>
            @endforelse
        </tbody>
    </x-admin.data-table>

    <div class="mt-6">{{ $faqs->links() }}</div>

</x-layouts.admin>