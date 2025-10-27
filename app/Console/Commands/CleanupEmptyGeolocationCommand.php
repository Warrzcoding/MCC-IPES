<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\LoginAttempt;

class CleanupEmptyGeolocationCommand extends Command
{
    protected $signature = 'cleanup:empty-geolocation';
    protected $description = 'Delete login attempt records with empty latitude, longitude, and location';

    public function handle()
    {
        $this->info('🔍 Scanning for login attempt records with empty geolocation data...');
        
        $count = LoginAttempt::where('latitude', '=', null)
            ->orWhere('longitude', '=', null)
            ->orWhere('location', '=', null)
            ->count();
        
        if ($count === 0) {
            $this->info('✅ No records with empty geolocation found.');
            return;
        }
        
        $this->warn("⚠️  Found {$count} records with empty geolocation data (null latitude, longitude, or location).");
        
        if (!$this->confirm('Delete these records? This action cannot be undone.')) {
            $this->info('Cancelled.');
            return;
        }
        
        $deleted = LoginAttempt::where('latitude', '=', null)
            ->orWhere('longitude', '=', null)
            ->orWhere('location', '=', null)
            ->delete();
        
        $this->info("✅ Successfully deleted {$deleted} records with empty geolocation data.");
        $this->line('Your login_attempts table is now clean.');
    }
}
