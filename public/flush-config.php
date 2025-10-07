<?php
/**
 * Temporary maintenance script to clear Laravel caches and remove the cached config file
 * when you cannot run artisan commands on the production host.
 *
 * Deployment steps:
 * 1. Deploy this file to production.
 * 2. Visit https://your-domain/flush-config.php once; expect to see "done".
 * 3. Delete this file immediately after it succeeds.
 */

use Illuminate\Contracts\Console\Kernel;
use Illuminate\Http\Request;

require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';

/** @var Kernel $kernel */
$kernel = $app->make(Kernel::class);

$request = Request::capture();
$kernel->bootstrap();

try {
    // Remove the cached config file if it exists so Laravel reloads .env
    $configCachePath = __DIR__ . '/../bootstrap/cache/config.php';
    if (file_exists($configCachePath)) {
        unlink($configCachePath);
    }

    $kernel->call('config:clear');
    $kernel->call('cache:clear');

    http_response_code(200);
    echo 'done';
} catch (Throwable $exception) {
    http_response_code(500);
    echo $exception->getMessage();
}