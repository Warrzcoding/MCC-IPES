<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\SuperAdmin;

class SuperAdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Clear existing super admins
        SuperAdmin::truncate();

        // Create default super admin
        SuperAdmin::create([
            'name' => 'Super Administrator',
            'email' => 'superadmin@mccipes.com',
            'password' => Hash::make('SuperAdmin@2024'), // Change this password!
            'last_login' => null,
        ]);

        $this->command->info('✅ Super Admin created successfully!');
        $this->command->info('📧 Email: superadmin@mccipes.com');
        $this->command->info('🔐 Password: SuperAdmin@2024');
        $this->command->info('⚠️  IMPORTANT: Change this password after first login!');
    }
}