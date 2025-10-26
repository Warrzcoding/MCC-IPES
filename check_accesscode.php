<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\SuperAdmin;

$super = SuperAdmin::first();
if ($super) {
    echo "Email: " . $super->email . "\n";
    echo "Accesscode Hash: " . $super->accesscode . "\n";
    echo "Hash starts with \$argon2id\$: " . (strpos($super->accesscode, '$argon2id$') === 0 ? 'YES' : 'NO') . "\n";
} else {
    echo "No super admin found\n";
}
