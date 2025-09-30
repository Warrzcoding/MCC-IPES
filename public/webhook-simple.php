<?php
/**
 * Simple GitHub Webhook Handler for Hostinger Shared Hosting
 * This is a simplified version that works better with shared hosting limitations
 */

// Configuration - UPDATE THESE VALUES
$secret = 'your_webhook_secret_here'; // Set this in your GitHub webhook settings
$branch = 'main'; // Change to 'master' if that's your main branch

// Log file for debugging
$log_file = __DIR__ . '/webhook.log';

// Function to log messages
function logMessage($message) {
    global $log_file;
    $timestamp = date('Y-m-d H:i:s');
    file_put_contents($log_file, "[$timestamp] $message\n", FILE_APPEND | LOCK_EX);
}

// Start logging
logMessage("=== Webhook Called ===");
logMessage("IP: " . ($_SERVER['REMOTE_ADDR'] ?? 'unknown'));
logMessage("Method: " . ($_SERVER['REQUEST_METHOD'] ?? 'unknown'));

// Verify POST request
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    logMessage("ERROR: Not a POST request");
    die('Method Not Allowed');
}

// Get payload
$payload = file_get_contents('php://input');
$headers = getallheaders();

logMessage("Headers received: " . json_encode($headers));

// Verify signature if secret is set
if (!empty($secret) && $secret !== 'your_webhook_secret_here') {
    $signature = $headers['X-Hub-Signature-256'] ?? $headers['x-hub-signature-256'] ?? '';
    $expected = 'sha256=' . hash_hmac('sha256', $payload, $secret);
    
    if (!hash_equals($expected, $signature)) {
        http_response_code(401);
        logMessage("ERROR: Invalid signature. Expected: $expected, Got: $signature");
        die('Unauthorized');
    }
    logMessage("Signature verified");
}

// Parse payload
$data = json_decode($payload, true);
if (json_last_error() !== JSON_ERROR_NONE) {
    http_response_code(400);
    logMessage("ERROR: Invalid JSON: " . json_last_error_msg());
    die('Bad Request');
}

// Check branch
$ref = $data['ref'] ?? '';
$target_ref = "refs/heads/$branch";

if ($ref !== $target_ref) {
    logMessage("Ignoring push to: $ref (target: $target_ref)");
    die('OK - Wrong branch');
}

logMessage("Processing push to $branch");

// Try to execute git pull
$commands = [
    'cd ' . __DIR__ . '/..',
    'git pull origin ' . $branch,
    'php artisan cache:clear',
    'php artisan config:clear',
    'php artisan view:clear'
];

$success = true;
foreach ($commands as $cmd) {
    logMessage("Executing: $cmd");
    
    $output = [];
    $return_code = 0;
    
    // Try exec first
    if (function_exists('exec')) {
        exec($cmd . ' 2>&1', $output, $return_code);
        $result = implode("\n", $output);
    } 
    // Try shell_exec as fallback
    elseif (function_exists('shell_exec')) {
        $result = shell_exec($cmd . ' 2>&1');
        $return_code = 0; // shell_exec doesn't return exit code
    } 
    // Try system as last resort
    elseif (function_exists('system')) {
        ob_start();
        system($cmd, $return_code);
        $result = ob_get_clean();
    } else {
        logMessage("ERROR: No shell execution functions available");
        $success = false;
        break;
    }
    
    logMessage("Result ($return_code): $result");
    
    if ($return_code !== 0 && strpos($cmd, 'git pull') !== false) {
        $success = false;
        break;
    }
}

if ($success) {
    logMessage("SUCCESS: Deployment completed");
    echo "Deployment successful";
} else {
    logMessage("ERROR: Deployment failed");
    http_response_code(500);
    echo "Deployment failed";
}

logMessage("=== Webhook Completed ===\n");
?>