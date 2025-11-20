<?php

require_once 'vendor/autoload.php';

use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Config;

// Load environment variables
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();

// Set up basic Laravel configuration
$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

echo "🧪 Testing SendGrid SMTP Configuration...\n\n";

try {
    echo "📧 Sending test email...\n";

    Mail::raw('🎉 SendGrid SMTP Test Successful!

Your MCC-IPES email configuration is working perfectly!

Configuration Details:
- Host: ' . config('mail.mailers.smtp.host') . '
- Port: ' . config('mail.mailers.smtp.port') . '
- Username: ' . config('mail.mailers.smtp.username') . '
- From: ' . config('mail.from.address') . '
- Encryption: ' . config('mail.mailers.smtp.encryption') . '

Time: ' . now() . '

This is a test email from your MCC-IPES system.', function ($message) {
        $message->to(config('mail.from.address'))
                ->subject('MCC-IPES Email Test - ' . now());
    });

    echo "\n✅ SUCCESS: Test email sent successfully!\n";
    echo "📧 Check your inbox: " . config('mail.from.address') . "\n";
    echo "📝 Subject: MCC-IPES Email Test\n\n";

    echo "🔧 Configuration used:\n";
    echo "   Host: " . config('mail.mailers.smtp.host') . "\n";
    echo "   Port: " . config('mail.mailers.smtp.port') . "\n";
    echo "   Username: " . config('mail.mailers.smtp.username') . "\n";
    echo "   From: " . config('mail.from.address') . "\n";
    echo "   Encryption: " . config('mail.mailers.smtp.encryption') . "\n";

} catch (\Exception $e) {
    echo "\n❌ ERROR: Email could not be sent.\n";
    echo "📋 Error details: " . $e->getMessage() . "\n\n";

    echo "🔧 Current configuration:\n";
    echo "   Host: " . config('mail.mailers.smtp.host') . "\n";
    echo "   Port: " . config('mail.mailers.smtp.port') . "\n";
    echo "   Username: " . config('mail.mailers.smtp.username') . "\n";
    echo "   From: " . config('mail.from.address') . "\n";
    echo "   Encryption: " . config('mail.mailers.smtp.encryption') . "\n";
}

echo "\n✨ Test completed!\n";