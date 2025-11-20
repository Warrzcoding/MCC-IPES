<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class TestRecaptcha extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'test:recaptcha';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Test reCAPTCHA configuration';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('🧪 Testing reCAPTCHA Configuration...');
        $this->newLine();

        // Check .env file directly
        $envFile = base_path('.env');
        $this->info('📋 .env File Contents (reCAPTCHA section):');
        if (file_exists($envFile)) {
            $envContent = file_get_contents($envFile);
            $lines = explode("\n", $envContent);
            foreach ($lines as $line) {
                if (strpos($line, 'RECAPTCHA') !== false) {
                    $this->line('   ' . $line);
                }
            }
        }
        $this->newLine();

        // Check basic app configuration
        $this->info('📋 Basic App Configuration:');
        $this->line('   APP_ENV: ' . env('APP_ENV'));
        $this->line('   APP_URL: ' . env('APP_URL'));
        $this->line('   APP_DEBUG: ' . env('APP_DEBUG'));
        $this->newLine();

        // Check environment variables
        $this->info('📋 Environment Variables:');
        $this->line('   RECAPTCHA_SITE_KEY_V3: ' . env('RECAPTCHA_SITE_KEY_V3'));
        $this->line('   RECAPTCHA_SECRET_KEY_V3: ' . env('RECAPTCHA_SECRET_KEY_V3'));
        $this->line('   RECAPTCHA_SITE_KEY_V3_LOCAL: ' . env('RECAPTCHA_SITE_KEY_V3_LOCAL'));
        $this->line('   RECAPTCHA_SECRET_KEY_V3_LOCAL: ' . env('RECAPTCHA_SECRET_KEY_V3_LOCAL'));
        $this->newLine();

        // Check config values
        $this->info('⚙️ Configuration Values:');
        $this->line('   services.recaptcha.site_key_v3: ' . config('services.recaptcha.site_key_v3'));
        $this->line('   services.recaptcha.secret_key_v3: ' . config('services.recaptcha.secret_key_v3'));
        $this->newLine();

        // Check if keys are configured
        $siteKey = config('services.recaptcha.site_key_v3');
        $secretKey = config('services.recaptcha.secret_key_v3');

        if (empty($siteKey) || empty($secretKey)) {
            $this->error('❌ ERROR: reCAPTCHA keys are not configured!');
            $this->error('   Site Key: ' . (empty($siteKey) ? 'MISSING' : 'SET'));
            $this->error('   Secret Key: ' . (empty($secretKey) ? 'MISSING' : 'SET'));
            $this->newLine();
            $this->info('💡 Solution: Check your .env file and ensure RECAPTCHA_SITE_KEY_V3 and RECAPTCHA_SECRET_KEY_V3 are set');
            $this->info('💡 Also run: php artisan config:clear && php artisan config:cache');
        } else {
            $this->info('✅ SUCCESS: reCAPTCHA keys are configured!');
            $this->info('   Site Key: ' . substr($siteKey, 0, 20) . '...');
            $this->info('   Secret Key: ' . substr($secretKey, 0, 20) . '...');
            $this->newLine();
            $this->info('🌐 Test URL: http://localhost/login');
            $this->info('🔍 Check browser console for reCAPTCHA script loading');
        }

        $this->newLine();
        $this->info('✨ Test completed!');
    }
}
