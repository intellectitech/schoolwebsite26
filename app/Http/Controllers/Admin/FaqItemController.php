<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreFaqItemRequest;
use App\Http\Requests\Admin\UpdateFaqItemRequest;
use App\Models\FaqItem;

class FaqItemController extends Controller
{
    public function index()
    {
        $faqs = FaqItem::orderBy('category')->orderBy('sort_order')->paginate(15);

        return view('admin.faqs.index', compact('faqs'));
    }

    public function create()
    {
        return view('admin.faqs.create');
    }

    public function store(StoreFaqItemRequest $request)
    {
        $data = $request->validated();
        $data['category'] = $data['category'] ?: 'admissions';

        FaqItem::create($data);

        return redirect()->route('admin.faqs.index')
            ->with('success', 'FAQ added successfully.');
    }

    public function edit(FaqItem $faq)
    {
        return view('admin.faqs.edit', compact('faq'));
    }

    public function update(UpdateFaqItemRequest $request, FaqItem $faq)
    {
        $data = $request->validated();
        $data['category'] = $data['category'] ?: 'admissions';

        $faq->update($data);

        return redirect()->route('admin.faqs.index')
            ->with('success', 'FAQ updated successfully.');
    }

    public function destroy(FaqItem $faq)
    {
        $faq->delete();

        return redirect()->route('admin.faqs.index')
            ->with('success', 'FAQ deleted.');
    }
}