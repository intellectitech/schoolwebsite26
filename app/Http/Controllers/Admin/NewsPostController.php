<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreNewsPostRequest;
use App\Http\Requests\Admin\UpdateNewsPostRequest;
use App\Models\NewsPost;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class NewsPostController extends Controller
{
    public function index()
    {
        // Newest-first regardless of published state, so drafts are visible
        // to admins here even though the public site hides them.
        $posts = NewsPost::orderByDesc('created_at')->paginate(10);

        return view('admin.news.index', compact('posts'));
    }

    public function create()
    {
        return view('admin.news.create');
    }

    public function store(StoreNewsPostRequest $request)
    {
        $data = $request->validated();

        $data['slug'] = $data['slug'] ?: Str::slug($data['title']);
        $data['is_featured'] = $request->boolean('is_featured');

        $imagePath = $request->file('image')->store('news', 'public');
        $data['image_url'] = Storage::url($imagePath);

        NewsPost::create($data);

        return redirect()->route('admin.news.index')
            ->with('success', 'News post created successfully.');
    }

    public function edit(NewsPost $news)
    {
        return view('admin.news.edit', ['post' => $news]);
    }

    public function update(UpdateNewsPostRequest $request, NewsPost $news)
    {
        $data = $request->validated();

        $data['slug'] = $data['slug'] ?: Str::slug($data['title']);
        $data['is_featured'] = $request->boolean('is_featured');

        if ($request->hasFile('image')) {
            // Remove the old file so uploads don't accumulate as orphaned
            // storage; only do this once the new upload has succeeded.
            $this->deleteStoredImage($news->image_url);

            $imagePath = $request->file('image')->store('news', 'public');
            $data['image_url'] = Storage::url($imagePath);
        }

        $news->update($data);

        return redirect()->route('admin.news.index')
            ->with('success', 'News post updated successfully.');
    }

    public function destroy(NewsPost $news)
    {
        $this->deleteStoredImage($news->image_url);
        $news->delete();

        return redirect()->route('admin.news.index')
            ->with('success', 'News post deleted.');
    }

    /**
     * Only deletes files that live on our own 'public' disk (i.e. were
     * actually uploaded through this admin panel) — seeded Unsplash URLs
     * are external and must never be passed to Storage::delete().
     */
    private function deleteStoredImage(?string $url): void
    {
        if (! $url || ! str_contains($url, '/storage/')) {
            return;
        }

        $path = Str::after($url, '/storage/');
        Storage::disk('public')->delete($path);
    }
}