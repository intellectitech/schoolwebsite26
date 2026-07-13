<?php

namespace Database\Seeders;

use App\Models\NewsPost;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class NewsPostSeeder extends Seeder
{
    public function run(): void
    {
        $posts = [
            [
                'title' => 'New Quantum Computing Lab Opens Doors for Advanced Physics Research',
                'category' => 'Research Breakthrough',
                'excerpt' => 'Starlight Academy marks a milestone with the inauguration of the Sirius Quantum Annex, a facility dedicated to sub-atomic computational studies.',
                'image_url' => '/images/quantum.jpg',
                'is_featured' => true,
                'published_at' => now()->subDays(2),
            ],
            [
                'title' => 'Spring Arts Gala Announced',
                'category' => 'Campus Life',
                'excerpt' => 'The annual celebration of fine arts and classical performance returns to the Great Hall this spring.',
                'image_url' => 'https://images.unsplash.com/photo-1465847899084-d164df4dedc6?q=80&w=800',
                'is_featured' => false,
                'published_at' => now()->subDays(5),
            ],
            [
                'title' => 'Digital Archives Expansion',
                'category' => 'Library',
                'excerpt' => 'Over 50,000 rare manuscripts have been digitized for global student access starting this term.',
                'image_url' => 'https://images.unsplash.com/photo-1507842217343-583bb7270b66?q=80&w=800',
                'is_featured' => false,
                'published_at' => now()->subDays(7),
            ],
        ];

        foreach ($posts as $post) {
            NewsPost::create([
                ...$post,
                'slug' => Str::slug($post['title']),
            ]);
        }
    }
}