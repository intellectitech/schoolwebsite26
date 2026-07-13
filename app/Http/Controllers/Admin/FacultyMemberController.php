<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreFacultyMemberRequest;
use App\Http\Requests\Admin\UpdateFacultyMemberRequest;
use App\Models\FacultyMember;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class FacultyMemberController extends Controller
{
    public function index()
    {
        $faculty = FacultyMember::orderBy('sort_order')->paginate(10);

        return view('admin.faculty.index', compact('faculty'));
    }

    public function create()
    {
        return view('admin.faculty.create');
    }

    public function store(StoreFacultyMemberRequest $request)
    {
        $data = $request->validated();
        $data['is_spotlighted'] = $request->boolean('is_spotlighted');
        $photoPath = $request->file('photo')->store('faculty', 'public');
        $data['photo_url'] = Storage::url($photoPath);

        FacultyMember::create($data);

        return redirect()->route('admin.faculty.index')
            ->with('success', 'Faculty member added successfully.');
    }

    public function edit(FacultyMember $faculty)
    {
        return view('admin.faculty.edit', compact('faculty'));
    }

    public function update(UpdateFacultyMemberRequest $request, FacultyMember $faculty)
    {
        $data = $request->validated();
        $data['is_spotlighted'] = $request->boolean('is_spotlighted');

        if ($request->hasFile('photo')) {
            $this->deleteStoredImage($faculty->photo_url);
            $photoPath = $request->file('photo')->store('faculty', 'public');
            $data['photo_url'] = Storage::url($photoPath);
        }

        $faculty->update($data);

        return redirect()->route('admin.faculty.index')
            ->with('success', 'Faculty member updated successfully.');
    }

    public function destroy(FacultyMember $faculty)
    {
        $this->deleteStoredImage($faculty->photo_url);
        $faculty->delete();

        return redirect()->route('admin.faculty.index')
            ->with('success', 'Faculty member removed.');
    }

    private function deleteStoredImage(?string $url): void
    {
        if (! $url || ! str_contains($url, '/storage/')) {
            return;
        }

        Storage::disk('public')->delete(Str::after($url, '/storage/'));
    }
}