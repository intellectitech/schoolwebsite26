<?php

namespace Database\Seeders;

use App\Models\FaqItem;
use Illuminate\Database\Seeder;

class FaqItemSeeder extends Seeder
{
    public function run(): void
    {
        $faqs = [
            [
                'question' => 'When is the deadline for Fall applications?',
                'answer' => 'All Fall admission applications, including transcripts and references, must be submitted by January 15th for priority consideration.',
                'sort_order' => 1,
            ],
            [
                'question' => 'Are international students eligible for aid?',
                'answer' => 'Yes. International students are eligible for merit-based scholarships, and a limited pool of need-based grants is available on a case-by-case basis.',
                'sort_order' => 2,
            ],
            [
                'question' => 'What standardized tests are required?',
                'answer' => 'Requirements vary by grade level. Middle and High School applicants typically submit SSAT or equivalent scores alongside their application.',
                'sort_order' => 3,
            ],
            [
                'question' => 'Can I schedule a private campus tour?',
                'answer' => 'Absolutely. Private tours can be booked through the Admissions office and are available on weekdays throughout the academic year.',
                'sort_order' => 4,
            ],
        ];

        foreach ($faqs as $faq) {
            FaqItem::create([...$faq, 'category' => 'admissions']);
        }
    }
}