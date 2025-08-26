<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\Staff;

echo "Fixing duplicate staff IDs...\n";

// Find all staff with duplicate staff_id '45454545'
$duplicateStaff = Staff::where('staff_id', '45454545')->get();

echo "Found " . $duplicateStaff->count() . " records with staff_id '45454545'\n";

if ($duplicateStaff->count() > 1) {
    // Keep the first record, delete the rest
    $firstRecord = $duplicateStaff->first();
    $recordsToDelete = $duplicateStaff->skip(1);
    
    foreach ($recordsToDelete as $record) {
        echo "Deleting duplicate record ID: " . $record->id . "\n";
        $record->delete();
    }
    
    echo "Duplicate records removed successfully.\n";
} else {
    echo "No duplicates to remove.\n";
}

// Verify the fix
$remainingStaff = Staff::where('staff_id', '45454545')->get();
echo "Remaining records with staff_id '45454545': " . $remainingStaff->count() . "\n"; 