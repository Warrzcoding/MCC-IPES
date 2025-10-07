<?php
/**
 * Temporary script to clear Laravel caches when artisan access is unavailable.
 *
 * Usage:
 * 1. Deploy this file.
 * 2. Visit https://your-domain/flush-config.php once.
 * 3. Confirm you see "done".
 * 4. Remove this file immediately after use.
 */

use Illuminate\Contracts\Console\Kernel;

require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';

/** @var Kernel $kernel */
$kernel = $app->make(Kernel::class);

try {
    $kernel->call('config:clear');
    $kernel->call('cache:clear');

    http_response_code(200);
    echo 'done';
} catch (Throwable $exception) {
    http_response_code(500);
    echo $exception->getMessage();
} finally {
    $kernel->terminate(request(), response());
}