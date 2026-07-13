<?php

namespace Database\Seeders;

use App\Models\FacultyMember;
use Illuminate\Database\Seeder;

class FacultyMemberSeeder extends Seeder
{
    public function run(): void
    {
        $faculty = [
            [
                'name' => 'Dr. Eleanor Vance',
                'title' => 'Dean of Humanities',
                'bio' => 'Oxford Fellow, 15+ years in adolescent literature and critical theory.',
                'photo_url' => 'https://images.unsplash.com/photo-1580489944761-15a19d654956?q=80&w=400',
                'sort_order' => 1,
            ],
            [
                'name' => 'Prof. Julian Chen',
                'title' => 'Head of Applied Sciences',
                'bio' => 'Former NASA Research Associate specializing in orbital mechanics and robotics.',
                'photo_url' => 'https://images.unsplash.com/photo-1519085360753-af0119f7cbe7?q=80&w=400',
                'sort_order' => 2,
            ],
            [
                'name' => 'Sarah Montague, MFA',
                'title' => 'Director of Fine Arts',
                'bio' => 'Award-winning sculptor with works exhibited in the Met and Tate Modern.',
                'photo_url' => 'https://images.unsplash.com/photo-1573496359142-b8d87734a5a2?q=80&w=400',
                'sort_order' => 3,
            ],
            [
                'name' => 'Dr. Alistair Thorne',
                'title' => 'Director of Mathematics',
                'bio' => "Lead author of 'The Geometry of Logic,' specializing in game theory and complex systems.",
                'photo_url' => 'https://images.unsplash.com/photo-1560250097-0b93528c311a?q=80&w=400',
                'sort_order' => 4,
            ],
        ];

        foreach ($faculty as $member) {
            FacultyMember::create($member);
        }
    }
}