<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreContactMessageRequest;
use App\Models\ContactDepartment;
use App\Models\ContactMessage;

class ContactController extends Controller
{
    public function show()
    {
        $departments = ContactDepartment::ordered()->get();

        return view('contact.show', compact('departments'));
    }

    public function store(StoreContactMessageRequest $request)
    {
        ContactMessage::create($request->validated());

        return redirect()
            ->route('contact.show')
            ->with('success', "Thanks for reaching out — we'll get back to you within 24 business hours.");
    }
}