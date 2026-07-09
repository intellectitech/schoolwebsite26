<?php

namespace Database\Seeders;

use App\Models\Testimonial;
use Illuminate\Database\Seeder;

class TestimonialSeeder extends Seeder
{
    public function run(): void
    {
        $testimonials = [
            [
                'student_name' => 'Sarah J. Jenkins',
                'student_class' => 'Class of 2024 - Biomedical Engineering',
                'quote' => "At Starlight, I wasn't just taught how to analyze data; I was challenged to think about the human impact of my research. The community here pushes you to be your best self every single day.",
                'photo_url' => 'https://images.unsplash.com/photo-1494790108377-be9c29b29330?q=80&w=200',
                'sort_order' => 1,
            ],
            [
                'student_name' => 'Marcus Thorne',
                'student_class' => 'Class of 2025 - Fine Arts',
                'quote' => "The mentorship from faculty has changed my perspective on what's possible. I found an interest in art I never knew existed, and a vision for a global creative career.",
                'photo_url' => 'https://images.unsplash.com/photo-1500648767791-00dcc994a43e?q=80&w=200',
                'sort_order' => 2,
            ],
        ];

        foreach ($testimonials as $testimonial) {
            Testimonial::create($testimonial);
        }
    }
}