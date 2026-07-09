@php $post = $post ?? null; @endphp

<div class="grid grid-cols-1 md:grid-cols-2 gap-5">
    <div class="md:col-span-2">
        <x-admin.form-field name="title" label="Title" required :value="$post?->title" />
    </div>

    <x-admin.form-field name="slug" label="Slug (optional — auto-generated from title if left blank)" :value="$post?->slug" />
    <x-admin.form-field name="category" label="Category" :value="$post?->category" placeholder="e.g. Research Breakthrough" />

    <div class="md:col-span-2">
        <x-admin.form-field name="excerpt" label="Excerpt" type="textarea" :rows="2" required :value="$post?->excerpt" />
    </div>

    <div class="md:col-span-2">
        <x-admin.form-field name="body" label="Full Body (optional)" type="textarea" :rows="6" :value="$post?->body" />
    </div>

    <div class="md:col-span-2">
        <x-input-label for="image" :value="'Cover Image' . ($post ? '' : ' *')" />

        @if($post?->image_url)
            <img src="{{ $post->image_url }}" alt="" class="w-32 h-32 rounded-lg object-cover mb-2">
            <p class="text-xs text-on-surface-variant mb-2">Current image — upload a new file below to replace it.</p>
        @endif

        <input
            type="file" id="image" name="image" accept="image/*"
            class="w-full text-sm text-on-surface-variant file:mr-4 file:py-2.5 file:px-4 file:rounded-lg file:border-0 file:bg-primary/5 file:text-primary file:font-label-md file:text-sm hover:file:bg-primary/10"
        >
        <x-input-error :messages="$errors->get('image')" class="mt-1" />
    </div>

    <div>
        <x-input-label for="published_at" value="Publish Date (leave blank to save as draft)" />
        <x-text-input
            type="datetime-local" id="published_at" name="published_at"
            :value="old('published_at', $post?->published_at?->format('Y-m-d\TH:i'))"
        />
        <x-input-error :messages="$errors->get('published_at')" class="mt-1" />
    </div>

    <div class="flex items-center gap-3 pt-8">
        <input
            type="checkbox" id="is_featured" name="is_featured" value="1"
            @checked(old('is_featured', $post?->is_featured))
            class="w-5 h-5 rounded border-outline-variant text-primary focus:ring-primary"
        >
        <x-input-label for="is_featured" value="Feature this post (shows large on the home page)" class="!mb-0" />
    </div>
</div>