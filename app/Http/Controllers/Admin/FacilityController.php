<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreFacilityRequest;
use App\Http\Requests\Admin\UpdateFacilityRequest;
use App\Models\Facility;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class FacilityController extends Controller
{
    public function index()
    {
        $facilities = Facility::orderBy('sort_order')->paginate(10);

        return view('admin.facilities.index', compact('facilities'));
    }

    public function create()
    {
        return view('admin.facilities.create');
    }

    public function store(StoreFacilityRequest $request)
    {
        $data = $request->validated();
        $data['is_featured'] = $request->boolean('is_featured');
        $imagePath = $request->file('image')->store('facilities', 'public');
        $data['image_url'] = Storage::url($imagePath);

        Facility::create($data);

        return redirect()->route('admin.facilities.index')
            ->with('success', 'Facility added successfully.');
    }

    public function edit(Facility $facility)
    {
        return view('admin.facilities.edit', compact('facility'));
    }

    public function update(UpdateFacilityRequest $request, Facility $facility)
    {
        $data = $request->validated();
        $data['is_featured'] = $request->boolean('is_featured');

        if ($request->hasFile('image')) {
            $this->deleteStoredImage($facility->image_url);
            $imagePath = $request->file('image')->store('facilities', 'public');
            $data['image_url'] = Storage::url($imagePath);
        }

        $facility->update($data);

        return redirect()->route('admin.facilities.index')
            ->with('success', 'Facility updated successfully.');
    }

    public function destroy(Facility $facility)
    {
        $this->deleteStoredImage($facility->image_url);
        $facility->delete();

        return redirect()->route('admin.facilities.index')
            ->with('success', 'Facility deleted.');
    }

    private function deleteStoredImage(?string $url): void
    {
        if (! $url || ! str_contains($url, '/storage/')) {
            return;
        }

        Storage::disk('public')->delete(Str::after($url, '/storage/'));
    }
}