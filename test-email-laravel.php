<?php

// Simple Laravel artisan command to test email
echo "🧪 Testing Gmail SMTP with Laravel...\n\n";

echo "📧 Sending test email using Laravel Mail...\n";
echo "⏳ Please wait...\n\n";

// Run Laravel artisan command to send test email
$command = 'php artisan tinker --execute="
use Illuminate\Support\Facades\Mail;
use Illuminate\Mail\Message;

try {
    Mail::raw(\'🎉 Gmail SMTP Test Successful!\n\nYour MCC-IPES email configuration is working perfectly!\n\nTime: \' . now() . \'\nFrom: MCC-IPES System\', function (Message \$message) {
        \$message->to(\'mccipesotp@gmail.com\')
                ->subject(\'MCC-IPES Email Test - \' . now());
    });
    
    echo \"✅ SUCCESS: Test email sent successfully!\";
    echo \"📧 Check your inbox: mccipesotp@gmail.com\";
} catch (Exception \$e) {
    echo \"❌ ERROR: \" . \$e->getMessage();
}
"';

echo $command . "\n\n";