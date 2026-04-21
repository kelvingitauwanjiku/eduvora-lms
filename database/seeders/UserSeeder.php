<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        $instructors = [
            ['name' => 'Sarah Johnson', 'email' => 'sarah@example.com', 'headline' => 'Senior Software Engineer at Google'],
            ['name' => 'Michael Chen', 'email' => 'michael@example.com', 'headline' => 'Full Stack Developer & Instructor'],
            ['name' => 'Emily Davis', 'email' => 'emily@example.com', 'headline' => 'Data Scientist'],
            ['name' => 'James Wilson', 'email' => 'james@example.com', 'headline' => 'DevOps Engineer'],
            ['name' => 'Lisa Anderson', 'email' => 'lisa@example.com', 'headline' => 'UX Designer'],
        ];

        foreach ($instructors as $instructor) {
            User::updateOrCreate(
                ['email' => $instructor['email']],
[
                    'name' => $instructor['name'],
                    'first_name' => explode(' ', $instructor['name'])[0],
                    'last_name' => explode(' ', $instructor['name'])[1],
                    'username' => strtolower(explode(' ', $instructor['name'])[0]) . rand(1, 999),
                    'email_verified_at' => now(),
                    'password' => Hash::make('password'),
                    'phone' => '+1' . rand(2000000000, 9999999999),
                    'avatar' => 'https://api.dicebear.com/7.x/avataaars/svg?seed=' . $instructor['name'],
                    'headline' => $instructor['headline'],
                    'bio' => 'Experienced instructor with 5+ years in the industry.',
                    'is_instructor' => true,
                    'is_verified' => true,
                    'is_featured' => true,
                    'status' => 'active',
                    'uuid' => \Illuminate\Support\Str::uuid()->toString(),
                    'referral_code' => strtoupper(\Illuminate\Support\Str::random(8)),
                ]
            );
        }

        $this->command->info('Created ' . count($instructors) . ' instructors');
        
        // Also create a demo student
        User::firstOrCreate(
            ['email' => 'student@demo.com'],
            [
                'name' => 'Demo Student',
                'first_name' => 'Demo',
                'last_name' => 'Student',
                'username' => 'demo',
                'email_verified_at' => now(),
                'password' => Hash::make('demo'),
                'status' => 'active',
                'uuid' => \Illuminate\Support\Str::uuid()->toString(),
            ]
        );

        // Create admin user
        User::firstOrCreate(
            ['email' => 'admin@eduvora.com'],
            [
                'name' => 'Admin User',
                'first_name' => 'Admin',
                'last_name' => 'User',
                'username' => 'admin',
                'email_verified_at' => now(),
                'password' => Hash::make('admin123'),
                'status' => 'active',
                'is_instructor' => true,
                'is_verified' => true,
                'role' => 'admin',
                'uuid' => \Illuminate\Support\Str::uuid()->toString(),
            ]
        );
    }
}