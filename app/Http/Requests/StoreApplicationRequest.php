<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreApplicationRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // public form — anyone can apply
    }

    public function rules(): array
    {
        return [
            'full_name' => ['required', 'string', 'max:255'],
            'date_of_birth' => ['required', 'date', 'before:today'],
            'email' => ['required', 'email', 'max:255'],
            'phone' => ['required', 'string', 'max:30'],

            'current_school' => ['required', 'string', 'max:255'],
            'grade_applying_for' => ['required', 'string', 'in:Grade 9,Grade 10,Grade 11,Grade 12'],
            'current_gpa' => ['nullable', 'string', 'max:20'],
            // Matches the design's stated constraint: "PDF, DOCX or JPEG (Max 10MB)"
            'transcript' => ['nullable', 'file', 'mimes:pdf,docx,jpg,jpeg', 'max:10240'],

            'personal_statement' => ['required', 'string', 'min:50', 'max:8000'],
        ];
    }

    public function messages(): array
    {
        return [
            'personal_statement.min' => 'Your personal statement seems too short — aim for the recommended 500-1000 words.',
            'transcript.mimes' => 'Transcripts must be a PDF, DOCX, or JPEG file.',
        ];
    }
}