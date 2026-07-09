<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Application;
use Illuminate\Http\Request;

class ApplicationController extends Controller
{
    public function index()
    {
        // Newest first, so staff see this week's applications without paging through old ones.
        $applications = Application::orderByDesc('created_at')->paginate(15);

        return view('admin.applications.index', compact('applications'));
    }

    public function show(Application $application)
    {
        return view('admin.applications.show', compact('application'));
    }

    /**
     * Applications aren't a content model an admin authors — there's no
     * create/edit/delete form, just a status update on an existing
     * submission. A dedicated method (rather than a full resource controller)
     * keeps that distinction clear.
     */
    public function updateStatus(Request $request, Application $application)
    {
        $request->validate([
            'status' => ['required', 'in:pending,reviewed,accepted,rejected'],
        ]);

        $application->update(['status' => $request->status]);

        return redirect()
            ->route('admin.applications.show', $application)
            ->with('success', 'Application status updated.');
    }
}