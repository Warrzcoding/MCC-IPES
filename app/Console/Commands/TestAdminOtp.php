<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Jobs\SendAdminOtpJob;

class TestAdminOtp extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'test:admin-otp {email?}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Test admin OTP email sending';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $email = $this->argument('email') ?: 'systemmccpes@gmail.com';

        $this->info('🧪 Testing Admin OTP Email...');
        $this->newLine();

        $this->info("📧 Sending test OTP to: {$email}");
        $this->info('🔢 Test OTP Code: 123456');

        try {
            // Dispatch the same job used in admin login
            SendAdminOtpJob::dispatch('123456', $email, 'Test Administrator');

            $this->newLine();
            $this->info('✅ SUCCESS: Admin OTP job dispatched!');
            $this->info("📧 Check inbox: {$email}");
            $this->info('📝 Subject: Administrator Login Verification Code');
            $this->info('🔢 OTP Code: 123456');
            $this->newLine();

            $this->info('💡 Note: If using queue, run "php artisan queue:work" to process the job');
            $this->info('💡 Or check storage/logs/laravel.log for delivery status');

        } catch (\Exception $e) {
            $this->newLine();
            $this->error('❌ ERROR: Failed to dispatch admin OTP job');
            $this->error('📋 Error: ' . $e->getMessage());
        }

        $this->newLine();
        $this->info('✨ Test completed!');
    }
}
