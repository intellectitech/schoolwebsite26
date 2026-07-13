<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreAdmissionStepRequest;
use App\Http\Requests\Admin\UpdateAdmissionStepRequest;
use App\Models\AdmissionStep;

class AdmissionStepController extends Controller
{
    public function index()
    {
        $steps = AdmissionStep::orderBy('sort_order')->get();

        // Small, fixed-size list (4 steps in the design) — pagination would be
        // overkill here, unlike News/Events/FAQs which can grow indefinitely.
        return view('admin.admission-steps.index', compact('steps'));
    }

    public function create()
    {
        return view('admin.admission-steps.create');
    }

    public function store(StoreAdmissionStepRequest $request)
    {
        AdmissionStep::create($request->validated());

        return redirect()->route('admin.admission-steps.index')
            ->with('success', 'Admission step added successfully.');
    }

    public function edit(AdmissionStep $admissionStep)
    {
        return view('admin.admission-steps.edit', ['step' => $admissionStep]);
    }

    public function update(UpdateAdmissionStepRequest $request, AdmissionStep $admissionStep)
    {
        $admissionStep->update($request->validated());

        return redirect()->route('admin.admission-steps.index')
            ->with('success', 'Admission step updated successfully.');
    }

    public function destroy(AdmissionStep $admissionStep)
    {
        $admissionStep->delete();

        return redirect()->route('admin.admission-steps.index')
            ->with('success', 'Admission step deleted.');
    }
}