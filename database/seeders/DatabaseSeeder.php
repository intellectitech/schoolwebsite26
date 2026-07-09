<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
{
    $this->call([
        AdminUserSeeder::class,
        NewsPostSeeder::class,
        EventSeeder::class,
        PathwaySeeder::class,
        FacultyMemberSeeder::class,
        AdmissionStepSeeder::class,
        FaqItemSeeder::class,
        GalleryItemSeeder::class,
        FacilitySeeder::class,
        TestimonialSeeder::class,
         ContactDepartmentSeeder::class,
    ]);
}
}
