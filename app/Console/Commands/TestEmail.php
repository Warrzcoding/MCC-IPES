<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;

class TestEmail extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'email:test';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Test Gmail SMTP configuration';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('🧪 Testing Gmail SMTP Configuration...');
        $this->newLine();

        try {
            $this->info('📧 Sending test email...');
            
            Mail::raw('🎉 Gmail SMTP Test Successful!

Your MCC-IPES email configuration is working perfectly!

Configuration Details:
- Host: ' . config('mail.mailers.smtp.host') . '
- Port: ' . config('mail.mailers.smtp.port') . '
- Username: ' . config('mail.mailers.smtp.username') . '
- From: ' . config('mail.from.address') . '
- Encryption: ' . config('mail.mailers.smtp.encryption') . '

Time: ' . now() . '

This is a test email from your MCC-IPES system.', function ($message) {
                $message->to(config('mail.mailers.smtp.username'))
                        ->subject('MCC-IPES Email Test - ' . now());
            });

            $this->newLine();
            $this->info('✅ SUCCESS: Test email sent successfully!');
            $this->info('📧 Check your inbox: ' . config('mail.mailers.smtp.username'));
            $this->info('📝 Subject: MCC-IPES Email Test');
            $this->newLine();
            
            $this->info('🔧 Configuration used:');
            $this->line('   Host: ' . config('mail.mailers.smtp.host'));
            $this->line('   Port: ' . config('mail.mailers.smtp.port'));
            $this->line('   Username: ' . config('mail.mailers.smtp.username'));
            $this->line('   From: ' . config('mail.from.address'));
            $this->line('   Encryption: ' . config('mail.mailers.smtp.encryption'));
            
        } catch (\Exception $e) {
            $this->newLine();
            $this->error('❌ ERROR: Email could not be sent.');
            $this->error('📋 Error details: ' . $e->getMessage());
            $this->newLine();
            
            $this->info('🔧 Current configuration:');
            $this->line('   Host: ' . config('mail.mailers.smtp.host'));
            $this->line('   Port: ' . config('mail.mailers.smtp.port'));
            $this->line('   Username: ' . config('mail.mailers.smtp.username'));
            $this->line('   From: ' . config('mail.from.address'));
            $this->line('   Encryption: ' . config('mail.mailers.smtp.encryption'));
        }

        $this->newLine();
        $this->info('✨ Test completed!');
    }
}
