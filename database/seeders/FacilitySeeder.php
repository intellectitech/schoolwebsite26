<?php

namespace Database\Seeders;

use App\Models\Facility;
use Illuminate\Database\Seeder;

class FacilitySeeder extends Seeder
{
    public function run(): void
    {
        $facilities = [
            [
                'name' => 'The Heritage Library',
                'description' => 'A sanctuary for intellectual pursuit and historical preservation.',
                'image_url' => 'https://images.unsplash.com/photo-1568667256549-094345857637?q=80&w=1200',
                'is_featured' => true,
                'sort_order' => 1,
            ],
            [
                'name' => 'Innovation Lab',
                'description' => 'State-of-the-art facilities for physics, chemistry, and robotics engineering.',
                'image_url' => 'https://images.unsplash.com/photo-1581092160562-40aa08e78837?q=80&w=800',
                'is_featured' => false,
                'sort_order' => 2,
            ],
            [
                'name' => 'Starlight Arena',
                'description' => 'Olympic-grade sports and fitness centers.',
                'image_url' => 'https://images.unsplash.com/photo-1546519638-68e109498ffc?q=80&w=800',
                'is_featured' => false,
                'sort_order' => 3,
            ],
        ];

        foreach ($facilities as $facility) {
            Facility::create($facility);
        }
    }
}