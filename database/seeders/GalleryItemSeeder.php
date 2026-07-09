<?php

namespace Database\Seeders;

use App\Models\GalleryItem;
use Illuminate\Database\Seeder;

class GalleryItemSeeder extends Seeder
{
    public function run(): void
    {
        $items = [
            [
                'title' => 'Athletics',
                'tagline' => 'Champions of Art and Grace',
                'image_url' => '/images/athletics.svg',
                'sort_order' => 1,
            ],
            [
                'title' => 'Service',
                'tagline' => 'Leaders for a Better Tomorrow',
                'image_url' => 'https://images.unsplash.com/photo-1522202176988-66273c2fd55f?q=80&w=800',
                'sort_order' => 2,
            ],
            [
                'title' => 'Events',
                'tagline' => 'Traditions That Unite Us',
                'image_url' => 'https://images.unsplash.com/photo-1492684223066-81342ee5ff30?q=80&w=800',
                'sort_order' => 3,
            ],
            [
                'title' => 'Creative Arts',
                'tagline' => 'Where Vision Finds Its Voice',
                'image_url' => 'https://images.unsplash.com/photo-1513364776144-60967b0f800f?q=80&w=800',
                'sort_order' => 4,
            ],
            [
                'title' => 'Innovation',
                'tagline' => 'The Frontiers of Discovery',
                'image_url' => 'https://images.unsplash.com/photo-1581091226825-a6a2a5aee158?q=80&w=800',
                'sort_order' => 5,
            ],
        ];

        foreach ($items as $item) {
            GalleryItem::create($item);
        }
    }
}