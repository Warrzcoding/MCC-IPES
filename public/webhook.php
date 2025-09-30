<?php
/**
 * GitHub Webhook Handler for Hostinger Shared Hosting
 * This script handles GitHub webhook calls to automatically deploy changes
 */

// Configuration
$secret = 'your_webhook_secret_here'; // Change this to match your GitHub webhook secret
$repo_path = '/path/to/your/repository'; // This will be the path on your Hostinger server
$branch = 'master'; // or 'master' - change to your main branch name

// Log file for debugging
$log_file = __DIR__ . '/webhook.log';

// Function to log messages
function logMessage($message) {
    global $log_file;
    $timestamp = date('Y-m-d H:i:s');
    file_put_contents($log_file, "[$timestamp] $message\n", FILE_APPEND | LOCK_EX);
}

// Function to execute shell commands safely
function executeCommand($command) {
    $output = [];
    $return_var = 0;
    exec($command . ' 2>&1', $output, $return_var);
    return [
        'output' => implode("\n", $output),
        'return_code' => $return_var
    ];
}

// Start logging
logMessage("Webhook called from IP: " . $_SERVER['REMOTE_ADDR']);

// Verify the request method
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    logMessage("Error: Invalid request method: " . $_SERVER['REQUEST_METHOD']);
    die('Method Not Allowed');
}

// Get the payload
$payload = file_get_contents('php://input');
$headers = getallheaders();

// Verify GitHub signature if secret is set
if (!empty($secret)) {
    $signature = isset($headers['X-Hub-Signature-256']) ? $headers['X-Hub-Signature-256'] : '';
    $expected_signature = 'sha256=' . hash_hmac('sha256', $payload, $secret);
    
    if (!hash_equals($expected_signature, $signature)) {
        http_response_code(401);
        logMessage("Error: Invalid signature");
        die('Unauthorized');
    }
    logMessage("Signature verified successfully");
}

// Parse the payload
$data = json_decode($payload, true);

if (json_last_error() !== JSON_ERROR_NONE) {
    http_response_code(400);
    logMessage("Error: Invalid JSON payload");
    die('Bad Request');
}

// Check if this is a push event to the correct branch
if (!isset($data['ref']) || $data['ref'] !== "refs/heads/$branch") {
    logMessage("Ignoring push to branch: " . ($data['ref'] ?? 'unknown'));
    die('OK - Not target branch');
}

logMessage("Processing push to $branch branch");

// Change to repository directory
if (!is_dir($repo_path)) {
    logMessage("Error: Repository path does not exist: $repo_path");
    die('Repository path not found');
}

chdir($repo_path);

// Execute git pull
logMessage("Executing git pull...");
$result = executeCommand('git pull origin ' . $branch);

if ($result['return_code'] === 0) {
    logMessage("Git pull successful: " . $result['output']);
    
    // Optional: Run additional commands after successful pull
    // Uncomment and modify as needed for your Laravel application
    
    // Clear Laravel cache
    logMessage("Clearing Laravel cache...");
    $cache_result = executeCommand('php artisan cache:clear');
    logMessage("Cache clear result: " . $cache_result['output']);
    
    // Clear config cache
    $config_result = executeCommand('php artisan config:clear');
    logMessage("Config clear result: " . $config_result['output']);
    
    // Clear view cache
    $view_result = executeCommand('php artisan view:clear');
    logMessage("View clear result: " . $view_result['output']);
    
    // Optional: Run composer install if composer.json changed
    if (isset($data['commits'])) {
        foreach ($data['commits'] as $commit) {
            if (isset($commit['modified']) && in_array('composer.json', $commit['modified'])) {
                logMessage("composer.json modified, running composer install...");
                $composer_result = executeCommand('composer install --no-dev --optimize-autoloader');
                logMessage("Composer install result: " . $composer_result['output']);
                break;
            }
        }
    }
    
    logMessage("Deployment completed successfully");
    echo "Deployment successful";
} else {
    logMessage("Git pull failed: " . $result['output']);
    http_response_code(500);
    echo "Deployment failed";
}

logMessage("Webhook processing completed\n");
?>