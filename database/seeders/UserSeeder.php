<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run()
    {
        // Create admin users
        $admins = [
            [
                'username' => 'admin',
                'email' => 'admin@example.com',
                'password' => Hash::make('admin123'),
                'full_name' => 'System Administrator',
                'role' => 'admin',
                'status' => 'active',
                'profile_image' => 'default-avatar.png'
            ],
            [
                'username' => 'superadmin',
                'email' => 'superadmin@example.com',
                'password' => Hash::make('super123'),
                'full_name' => 'Super Administrator',
                'role' => 'admin',
                'status' => 'active',
                'profile_image' => 'default-avatar.png'
            ]
        ];

        foreach ($admins as $admin) {
            User::create($admin);
        }

        // Create student users
        $students = [
            [
                'username' => 'john.doe',
                'email' => 'john.doe@example.com',
                'password' => Hash::make('student123'),
                'full_name' => 'John Doe',
                'school_id' => '2024-0001',
                'role' => 'student',
                'course' => 'BSIT',
                'status' => 'active',
                'profile_image' => 'default-avatar.png'
            ],
            [
                'username' => 'jane.smith',
                'email' => 'jane.smith@example.com',
                'password' => Hash::make('student123'),
                'full_name' => 'Jane Smith',
                'school_id' => '2024-0002',
                'role' => 'student',
                'course' => 'BSCS',
                'status' => 'active',
                'profile_image' => 'default-avatar.png'
            ],
            [
                'username' => 'mike.johnson',
                'email' => 'mike.johnson@example.com',
                'password' => Hash::make('student123'),
                'full_name' => 'Mike Johnson',
                'school_id' => '2024-0003',
                'role' => 'student',
                'course' => 'BSCE',
                'status' => 'active',
                'profile_image' => 'default-avatar.png'
            ],
            [
                'username' => 'sarah.wilson',
                'email' => 'sarah.wilson@example.com',
                'password' => Hash::make('student123'),
                'full_name' => 'Sarah Wilson',
                'school_id' => '2024-0004',
                'role' => 'student',
                'course' => 'BSEE',
                'status' => 'active',
                'profile_image' => 'default-avatar.png'
            ],
            [
                'username' => 'david.brown',
                'email' => 'david.brown@example.com',
                'password' => Hash::make('student123'),
                'full_name' => 'David Brown',
                'school_id' => '2024-0005',
                'role' => 'student',
                'course' => 'BSME',
                'status' => 'active',
                'profile_image' => 'default-avatar.png'
            ]
        ];

        foreach ($students as $student) {
            User::create($student);
        }
    }
}