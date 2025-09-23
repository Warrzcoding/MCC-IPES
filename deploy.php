<?php

$commands = [
    'composer install --optimize-autoloader --no-dev',
    'php artisan config:clear',
    'php artisan cache:clear',
    'php artisan route:clear',
    'php artisan view:clear',
    'php artisan optimize',
];

$output = "\n";
$output .= "Deployment started at " . date('Y-m-d H:i:s') . "\n";

foreach ($commands as $command) {
    $output .= sprintf("\nExecuting: %s\n", $command);
    $lastLine = system($command, $returnValue);
    $output .= sprintf("Return value: %d\n", $returnValue);
}

// Create storage symlink if it doesn't exist
if (!file_exists('public/storage')) {
    symlink('../storage/app/public', 'public/storage');
}

// Set proper permissions
$dirsToChange = [
    'storage',
    'storage/logs',
    'storage/framework',
    'storage/framework/sessions',
    'storage/framework/views',
    'storage/framework/cache',
    'bootstrap/cache'
];

foreach ($dirsToChange as $dir) {
    if (file_exists($dir)) {
        chmod($dir, 0775);
    }
}

$output .= "\nDeployment finished at " . date('Y-m-d H:i:s') . "\n";

// Log the deployment
file_put_contents('storage/logs/deploy.log', $output, FILE_APPEND);

echo $output;