<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class MainAdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Find the first admin user and make them the main admin
        $firstAdmin = User::where('role', 'admin')->first();
        
        if ($firstAdmin) {
            $firstAdmin->update(['is_main_admin' => true]);
            $this->command->info("User '{$firstAdmin->full_name}' has been set as the main admin.");
        } else {
            // If no admin exists, create one
            $mainAdmin = User::create([
                'username' => 'mainadmin',
                'email' => 'mainadmin@example.com',
                'password' => Hash::make('password123'),
                'full_name' => 'Main Administrator',
                'role' => 'admin',
                'status' => 'active',
                'is_main_admin' => true,
                'course' => 'Administration'
            ]);
            
            $this->command->info("Main admin created with username: mainadmin, password: password123");
        }
    }
} 