<?php

namespace Database\Seeders;

use App\Models\AdmissionStep;
use Illuminate\Database\Seeder;

class AdmissionStepSeeder extends Seeder
{
    public function run(): void
    {
        $steps = [
            [
                'step_number' => 1,
                'title' => 'Inquiry',
                'description' => 'Submit your initial interest and request a prospectus through our digital portal.',
                'icon' => 'edit_note',
                'sort_order' => 1,
            ],
            [
                'step_number' => 2,
                'title' => 'Application',
                'description' => 'Complete the comprehensive application including transcripts and references.',
                'icon' => 'description',
                'sort_order' => 2,
            ],
            [
                'step_number' => 3,
                'title' => 'Interview',
                'description' => 'A personal dialogue with our faculty to explore your passions and goals.',
                'icon' => 'forum',
                'sort_order' => 3,
            ],
            [
                'step_number' => 4,
                'title' => 'Enrollment',
                'description' => 'Upon acceptance, finalize your registration and join the Starlight cohort.',
                'icon' => 'school',
                'sort_order' => 4,
            ],
        ];

        foreach ($steps as $step) {
            AdmissionStep::create($step);
        }
    }
}