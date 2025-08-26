<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\Staff;

echo "Checking staff IDs for duplicates...\n";

$staffIds = Staff::pluck('staff_id')->toArray();
$duplicates = array_diff_assoc($staffIds, array_unique($staffIds));

if (empty($duplicates)) {
    echo "No duplicate staff IDs found.\n";
} else {
    echo "Duplicate staff IDs found:\n";
    foreach ($duplicates as $duplicate) {
        echo "- $duplicate\n";
    }
}

echo "\nAll staff IDs:\n";
foreach ($staffIds as $id) {
    echo "- $id\n";
} 