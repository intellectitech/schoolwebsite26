<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreGalleryItemRequest;
use App\Http\Requests\Admin\UpdateGalleryItemRequest;
use App\Models\GalleryItem;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class GalleryItemController extends Controller
{
    public function index()
    {
        $galleryItems = GalleryItem::orderBy('sort_order')->paginate(10);

        return view('admin.gallery.index', compact('galleryItems'));
    }

    public function create()
    {
        return view('admin.gallery.create');
    }

    public function store(StoreGalleryItemRequest $request)
    {
        $data = $request->validated();
        $imagePath = $request->file('image')->store('gallery', 'public');
        $data['image_url'] = Storage::url($imagePath);

        GalleryItem::create($data);

        return redirect()->route('admin.gallery.index')
            ->with('success', 'Gallery item added successfully.');
    }

    public function edit(GalleryItem $gallery)
    {
        return view('admin.gallery.edit', ['item' => $gallery]);
    }

    public function update(UpdateGalleryItemRequest $request, GalleryItem $gallery)
    {
        $data = $request->validated();

        if ($request->hasFile('image')) {
            $this->deleteStoredImage($gallery->image_url);
            $imagePath = $request->file('image')->store('gallery', 'public');
            $data['image_url'] = Storage::url($imagePath);
        }

        $gallery->update($data);

        return redirect()->route('admin.gallery.index')
            ->with('success', 'Gallery item updated successfully.');
    }

    public function destroy(GalleryItem $gallery)
    {
        $this->deleteStoredImage($gallery->image_url);
        $gallery->delete();

        return redirect()->route('admin.gallery.index')
            ->with('success', 'Gallery item deleted.');
    }

    private function deleteStoredImage(?string $url): void
    {
        if (! $url || ! str_contains($url, '/storage/')) {
            return;
        }

        Storage::disk('public')->delete(Str::after($url, '/storage/'));
    }
}