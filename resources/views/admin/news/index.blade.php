<x-layouts.admin :title="'News Posts'" :active="'news'">

    <x-admin.flash-message />
    <x-admin.page-header title="News Posts" create-route="admin.news.create" create-label="Add News Post" />

    <x-admin.data-table empty-message="No news posts yet.">
        <thead class="bg-surface-container-low">
            <tr class="text-left">
                <th class="px-5 py-3 font-label-md text-label-md text-on-surface-variant">Post</th>
                <th class="px-5 py-3 font-label-md text-label-md text-on-surface-variant">Category</th>
                <th class="px-5 py-3 font-label-md text-label-md text-on-surface-variant">Status</th>
                <th class="px-5 py-3 font-label-md text-label-md text-on-surface-variant">Published</th>
                <th class="px-5 py-3"></th>
            </tr>
        </thead>
        <tbody class="divide-y divide-outline-variant/10">
            @forelse($posts as $post)
                <tr class="hover:bg-surface-container-low/50 transition-colors">
                    <td class="px-5 py-3">
                        <div class="flex items-center gap-3">
                            @php
                                $postImage = $post->image_url ?: (str_contains(strtolower($post->title), 'quantum') ? '/images/quantum.jpg' : null);
                            @endphp
                            <img src="{{ $postImage ?? '/images/quantum.jpg' }}" alt="" class="w-12 h-12 rounded-lg object-cover flex-shrink-0">
                            <div class="min-w-0">
                                <p class="font-body-md text-sm font-semibold text-on-surface truncate max-w-xs">{{ $post->title }}</p>
                                @if($post->is_featured)
                                    <span class="inline-block bg-secondary-fixed/20 text-secondary text-[10px] px-2 py-0.5 rounded-full mt-1">Featured</span>
                                @endif
                            </div>
                        </div>
                    </td>
                    <td class="px-5 py-3 text-sm text-on-surface-variant">{{ $post->category ?? '—' }}</td>
                    <td class="px-5 py-3">
                        @if($post->published_at && $post->published_at->isPast())
                            <span class="text-xs font-semibold text-green-700 bg-green-100 px-2 py-1 rounded-full">Published</span>
                        @else
                            <span class="text-xs font-semibold text-on-surface-variant bg-surface-container px-2 py-1 rounded-full">Draft</span>
                        @endif
                    </td>
                    <td class="px-5 py-3 text-sm text-on-surface-variant">
                        {{ $post->published_at?->format('M j, Y') ?? '—' }}
                    </td>
                    <td class="px-5 py-3">
                        <div class="flex items-center justify-end gap-3">
                            <a href="{{ route('admin.news.edit', $post) }}" class="text-primary hover:text-primary-container" title="Edit">
                                <x-icon name="edit" class="text-xl" />
                            </a>
                            <x-admin.delete-form :action="route('admin.news.destroy', $post)" />
                        </div>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="5" class="px-5 py-10 text-center text-sm text-on-surface-variant">No news posts yet.</td>
                </tr>
            @endforelse
        </tbody>
    </x-admin.data-table>

    <div class="mt-6">{{ $posts->links() }}</div>

</x-layouts.admin>