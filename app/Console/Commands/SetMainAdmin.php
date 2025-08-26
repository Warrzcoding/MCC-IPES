<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;

class SetMainAdmin extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'admin:set-main {username?}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Set a user as main admin or list current admins';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $username = $this->argument('username');
        
        // List all admin users
        $admins = User::where('role', 'admin')->get();
        
        if ($admins->isEmpty()) {
            $this->error('No admin users found!');
            return 1;
        }
        
        $this->info('Current admin users:');
        $this->table(
            ['ID', 'Username', 'Full Name', 'Email', 'Main Admin'],
            $admins->map(function($admin) {
                return [
                    $admin->id,
                    $admin->username,
                    $admin->full_name,
                    $admin->email,
                    $admin->is_main_admin ? 'Yes' : 'No'
                ];
            })
        );
        
        // If username provided, set as main admin
        if ($username) {
            $admin = User::where('role', 'admin')->where('username', $username)->first();
            
            if (!$admin) {
                $this->error("Admin user with username '{$username}' not found!");
                return 1;
            }
            
            // Remove main admin from all other users
            User::where('role', 'admin')->update(['is_main_admin' => false]);
            
            // Set the specified user as main admin
            $admin->update(['is_main_admin' => true]);
            
            $this->info("User '{$admin->full_name}' ({$admin->username}) has been set as main admin!");
        } else {
            $this->info('To set a main admin, run: php artisan admin:set-main <username>');
        }
        
        return 0;
    }
} 