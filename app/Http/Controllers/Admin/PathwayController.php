<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StorePathwayRequest;
use App\Http\Requests\Admin\UpdatePathwayRequest;
use App\Models\Pathway;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class PathwayController extends Controller
{
    public function index()
    {
        $pathways = Pathway::orderBy('sort_order')->get();

        return view('admin.pathways.index', compact('pathways'));
    }

    public function create()
    {
        return view('admin.pathways.create');
    }

    public function store(StorePathwayRequest $request)
    {
        $data = $request->validated();
        $imagePath = $request->file('image')->store('pathways', 'public');
        $data['image_url'] = Storage::url($imagePath);

        Pathway::create($data);

        return redirect()->route('admin.pathways.index')
            ->with('success', 'Pathway added successfully.');
    }

    public function edit(Pathway $pathway)
    {
        return view('admin.pathways.edit', compact('pathway'));
    }

    public function update(UpdatePathwayRequest $request, Pathway $pathway)
    {
        $data = $request->validated();

        if ($request->hasFile('image')) {
            $this->deleteStoredImage($pathway->image_url);
            $imagePath = $request->file('image')->store('pathways', 'public');
            $data['image_url'] = Storage::url($imagePath);
        }

        $pathway->update($data);

        return redirect()->route('admin.pathways.index')
            ->with('success', 'Pathway updated successfully.');
    }

    public function destroy(Pathway $pathway)
    {
        $this->deleteStoredImage($pathway->image_url);
        $pathway->delete();

        return redirect()->route('admin.pathways.index')
            ->with('success', 'Pathway deleted.');
    }

    private function deleteStoredImage(?string $url): void
    {
        if (! $url || ! str_contains($url, '/storage/')) {
            return;
        }

        Storage::disk('public')->delete(Str::after($url, '/storage/'));
    }
}