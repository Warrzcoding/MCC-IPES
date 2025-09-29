<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\LoginAttempt;
use App\Models\User;
use Carbon\Carbon;

class LoginAttemptsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Sample IP addresses with known locations for testing
        $sampleData = [
            [
                'ip' => '8.8.8.8',
                'latitude' => 37.42301,
                'longitude' => -122.083352,
                'location' => 'Mountain View, California, United States',
                'status' => 'success'
            ],
            [
                'ip' => '1.1.1.1',
                'latitude' => 37.7749,
                'longitude' => -122.4194,
                'location' => 'San Francisco, California, United States',
                'status' => 'failed'
            ],
            [
                'ip' => '208.67.222.222',
                'latitude' => 40.7128,
                'longitude' => -74.0060,
                'location' => 'New York, New York, United States',
                'status' => 'success'
            ],
            [
                'ip' => '9.9.9.9',
                'latitude' => 51.5074,
                'longitude' => -0.1278,
                'location' => 'London, England, United Kingdom',
                'status' => 'failed'
            ],
            [
                'ip' => '208.67.220.220',
                'latitude' => 35.6762,
                'longitude' => 139.6503,
                'location' => 'Tokyo, Japan',
                'status' => 'success'
            ],
            [
                'ip' => '127.0.0.1',
                'latitude' => null,
                'longitude' => null,
                'location' => 'Local/Private Network',
                'status' => 'success'
            ]
        ];

        // Get some users for testing
        $users = User::limit(3)->get();
        
        if ($users->isEmpty()) {
            $this->command->warn('No users found. Please create some users first.');
            return;
        }

        $userAgents = [
            'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120.0.0.0 Safari/537.36',
            'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120.0.0.0 Safari/537.36',
            'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120.0.0.0 Safari/537.36',
            'Mozilla/5.0 (iPhone; CPU iPhone OS 17_0 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/17.0 Mobile/15E148 Safari/604.1',
            'Mozilla/5.0 (iPad; CPU OS 17_0 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/17.0 Mobile/15E148 Safari/604.1'
        ];

        // Create login attempts for the past 7 days
        foreach ($sampleData as $sample) {
            foreach ($users as $user) {
                // Create 1-3 attempts per user per sample
                $attemptCount = rand(1, 3);
                
                for ($i = 0; $i < $attemptCount; $i++) {
                    LoginAttempt::create([
                        'user_id' => $user->id,
                        'email' => $user->email,
                        'ip_address' => $sample['ip'],
                        'user_agent' => $userAgents[array_rand($userAgents)],
                        'status' => $sample['status'],
                        'latitude' => $sample['latitude'],
                        'longitude' => $sample['longitude'],
                        'location' => $sample['location'],
                        'created_at' => Carbon::now()->subDays(rand(0, 7))->subHours(rand(0, 23))->subMinutes(rand(0, 59)),
                        'updated_at' => Carbon::now()->subDays(rand(0, 7))->subHours(rand(0, 23))->subMinutes(rand(0, 59)),
                    ]);
                }
            }
        }

        $this->command->info('Login attempts with geolocation data seeded successfully!');
    }
}