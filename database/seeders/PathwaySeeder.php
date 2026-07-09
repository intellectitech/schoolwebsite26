<?php

namespace Database\Seeders;

use App\Models\Pathway;
use Illuminate\Database\Seeder;

class PathwaySeeder extends Seeder
{
    public function run(): void
    {
        $pathways = [
            [
                'title' => 'Early Childhood',
                'description' => 'Nurturing curiosity and social-emotional growth through play-based inquiry and sensory exploration.',
                'image_url' => 'https://images.unsplash.com/photo-1503676260728-1c00da094a0b?q=80&w=800',
                'sort_order' => 1,
            ],
            [
                'title' => 'Primary School',
                'description' => 'Building foundational literacy and numeracy while fostering a global perspective across diverse subjects.',
                'image_url' => 'https://images.unsplash.com/photo-1497633762265-9d179a990aa6?q=80&w=800',
                'sort_order' => 2,
            ],
            [
                'title' => 'Middle School',
                'description' => 'Empowering adolescents to find their voice through specialized electives and independent research projects.',
                'image_url' => 'https://images.unsplash.com/photo-1523240795612-9a054b0db644?q=80&w=800',
                'sort_order' => 3,
            ],
            [
                'title' => 'High School',
                'description' => 'Advanced placement and international baccalaureate programs designed for seamless transition to top-tier universities.',
                'image_url' => 'https://images.unsplash.com/photo-1541339907198-e08756dedf3f?q=80&w=800',
                'sort_order' => 4,
            ],
        ];

        foreach ($pathways as $pathway) {
            Pathway::create($pathway);
        }
    }
}