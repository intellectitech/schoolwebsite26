<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreNewsPostRequest extends FormRequest
{
    public function authorize(): bool
    {
        // Route-level 'auth' + 'admin' middleware already gates access to this
        // form; this stays true rather than duplicating that role check here.
        return true;
    }

    public function rules(): array
    {
        return [
            'title' => ['required', 'string', 'max:255'],
            'slug' => ['nullable', 'string', 'max:255', 'unique:news_posts,slug'],
            'category' => ['nullable', 'string', 'max:100'],
            'excerpt' => ['required', 'string', 'max:500'],
            'body' => ['nullable', 'string'],
            'image' => ['required', 'image', 'max:4096'], // 4MB, matches UpdateNewsPostRequest's optional version
            'is_featured' => ['sometimes', 'boolean'],
            'published_at' => ['nullable', 'date'],
        ];
    }

    public function messages(): array
    {
        return [
            'image.required' => 'Please upload a cover image for this post.',
            'excerpt.max' => 'Keep the excerpt under 500 characters — it displays as a short preview on the public site.',
        ];
    }
}