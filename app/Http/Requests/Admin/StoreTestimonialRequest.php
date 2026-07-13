<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class StoreTestimonialRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'student_name' => ['required', 'string', 'max:255'],
            'student_class' => ['required', 'string', 'max:255'],
            'quote' => ['required', 'string', 'max:1000'],
            'photo' => ['nullable', 'image', 'max:4096'], // optional — falls back to initial avatar, per Module 5
            'sort_order' => ['nullable', 'integer', 'min:0'],
        ];
    }
}