<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class StoreFaqItemRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'question' => ['required', 'string', 'max:255'],
            'answer' => ['required', 'string', 'max:1000'],
            // Free-text for now rather than a hardcoded enum — 'admissions' is the
            // only category seeded so far, but Module 5's design already anticipated
            // reusing FaqItem for other pages (e.g. 'campus') without a migration.
            'category' => ['nullable', 'string', 'max:100'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
        ];
    }
}