<?php

namespace Database\Seeders;

use App\Models\ContactDepartment;
use Illuminate\Database\Seeder;

class ContactDepartmentSeeder extends Seeder
{
    public function run(): void
    {
        $departments = [
            [
                'name' => 'Admissions Office',
                'description' => 'Prospective student inquiries',
                'phone' => '+1 (555) 100-2010',
                'email' => 'admissions@lyceumacademy.edu',
                'is_emergency' => false,
                'sort_order' => 1,
            ],
            [
                'name' => 'General Inquiry',
                'description' => 'General administrative questions',
                'phone' => '+1 (555) 100-2000',
                'email' => null,
                'is_emergency' => false,
                'sort_order' => 2,
            ],
            [
                'name' => 'Athletics Department',
                'description' => 'Sports programs and schedules',
                'phone' => '+1 (555) 100-3045',
                'email' => null,
                'is_emergency' => false,
                'sort_order' => 3,
            ],
            [
                'name' => 'Campus Security',
                'description' => 'Available 24/7 for Emergencies',
                'phone' => '+1 (555) 100-9111',
                'email' => null,
                'is_emergency' => true,
                'sort_order' => 4,
            ],
        ];

        foreach ($departments as $department) {
            ContactDepartment::create($department);
        }
    }
}