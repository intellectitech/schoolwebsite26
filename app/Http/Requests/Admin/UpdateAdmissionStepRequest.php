<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class UpdateAdmissionStepRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'step_number' => ['required', 'integer', 'min:1', 'max:99'],
            'title' => ['required', 'string', 'max:100'],
            'description' => ['required', 'string', 'max:300'],
            'icon' => ['required', 'string', 'max:50'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
        ];
    }
}