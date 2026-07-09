<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    public function run(): void
    {
        // updateOrCreate so re-running db:seed never duplicates the account
        // or silently overwrites a password you've since changed via the UI.
        User::updateOrCreate(
            ['email' => 'admin@lyceumacademy.test'],
            [
                'name' => 'Lyceum Admin',
                'password' => Hash::make('password'), // change immediately after first login
                'role' => 'admin',
                'email_verified_at' => now(),
            ]
        );
    }
}