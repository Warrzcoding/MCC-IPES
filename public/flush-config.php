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

use ErrorException;
use Illuminate\Contracts\Console\Kernel;
use Illuminate\Http\Request;

header('Content-Type: text/plain; charset=utf-8');
error_reporting(E_ALL);
ini_set('display_errors', '1');

set_error_handler(function ($severity, $message, $file, $line) {
    throw new ErrorException($message, 0, $severity, $file, $line);
});

register_shutdown_function(function () {
    $error = error_get_last();
    if ($error !== null && in_array($error['type'], [E_ERROR, E_PARSE, E_CORE_ERROR, E_COMPILE_ERROR], true)) {
        http_response_code(500);
        echo 'Fatal error: ' . $error['message'] . ' in ' . $error['file'] . ':' . $error['line'];
    }
});

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
        if (!is_writable($configCachePath)) {
            throw new ErrorException('config.php exists but is not writable: ' . $configCachePath);
        }
        if (!unlink($configCachePath)) {
            throw new ErrorException('Unable to delete cached config file: ' . $configCachePath);
        }
    }

    $kernel->call('config:clear');
    $kernel->call('cache:clear');

    http_response_code(200);
    echo 'done';
} catch (Throwable $exception) {
    $message = sprintf('%s: %s', get_class($exception), $exception->getMessage());
    $logPath = __DIR__ . '/../storage/logs/flush-config.log';
    @file_put_contents($logPath, '[' . date('c') . '] ' . $message . PHP_EOL, FILE_APPEND);

    http_response_code(500);
    echo $message;
}