<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreApplicationRequest;
use App\Models\Application;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ApplicationController extends Controller
{
    public function create()
    {
        return view('admissions.apply');
    }

    public function store(StoreApplicationRequest $request)
    {
        $data = $request->validated();

        if ($request->hasFile('transcript')) {
            $data['transcript_path'] = $request->file('transcript')->store('transcripts', 'public');
        }

        Application::create($data);

        return redirect()
            ->route('apply.create')
            ->with('success', 'Application successfully submitted! You will receive a confirmation email shortly.');
    }
}