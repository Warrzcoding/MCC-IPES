<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\LoginAttempt;
use App\Models\User;
use App\Services\GeolocationService;

class TestGeolocationCommand extends Command
{
    protected $signature = 'test:geolocation';
    protected $description = 'Create test login attempts with real geolocation data';

    public function handle()
    {
        $this->info('Creating test login attempts with real geolocation data...');
        
        $geolocationService = app(GeolocationService::class);
        
        // Test IPs from different locations
        $testIPs = [
            '8.8.8.8',      // Google DNS - Mountain View, CA
            '1.1.1.1',      // Cloudflare - Brisbane, Australia  
            '208.67.222.222', // OpenDNS - San Francisco, CA
            '134.195.196.26', // Germany
            '103.28.248.1',   // Singapore
        ];
        
        $testEmails = [
            'test.user1@example.com',
            'test.user2@example.com', 
            'test.user3@example.com',
            'test.user4@example.com',
            'test.user5@example.com',
        ];
        
        $userAgents = [
            'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/91.0.4472.124 Safari/537.36',
            'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/91.0.4472.124 Safari/537.36',
            'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/91.0.4472.124 Safari/537.36',
            'Mozilla/5.0 (iPhone; CPU iPhone OS 14_6 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/14.0 Mobile/15E148 Safari/604.1',
            'Mozilla/5.0 (Android 11; Mobile; rv:68.0) Gecko/68.0 Firefox/88.0',
        ];
        
        foreach ($testIPs as $index => $ip) {
            $this->info("Processing IP: {$ip}");
            
            // Get geolocation data
            $locationData = $geolocationService->getLocationData($ip);
            $this->line("  Location: {$locationData['location']}");
            
            // Create successful login attempt
            LoginAttempt::create([
                'user_id' => null,
                'email' => $testEmails[$index],
                'ip_address' => $ip,
                'user_agent' => $userAgents[$index],
                'status' => 'success',
                'latitude' => $locationData['latitude'],
                'longitude' => $locationData['longitude'],
                'location' => $locationData['location'],
                'created_at' => now()->subMinutes(rand(1, 60)),
            ]);
            
            // Create failed login attempt
            LoginAttempt::create([
                'user_id' => null,
                'email' => $testEmails[$index],
                'ip_address' => $ip,
                'user_agent' => $userAgents[$index],
                'status' => 'failed',
                'latitude' => $locationData['latitude'],
                'longitude' => $locationData['longitude'],
                'location' => $locationData['location'],
                'created_at' => now()->subMinutes(rand(61, 120)),
            ]);
        }
        
        $this->info('✅ Test login attempts created successfully!');
        $this->info('You can now view them in the login monitor with real geolocation data.');
    }
}