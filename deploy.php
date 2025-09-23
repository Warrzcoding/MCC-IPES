<?php

class Deployer {
    private $output = '';
    private $basePath;

    public function __construct() {
        $this->basePath = dirname(__FILE__);
        $this->log("Deployment started at " . date('Y-m-d H:i:s'));
    }

    private function log($message) {
        $this->output .= "[" . date('Y-m-d H:i:s') . "] " . $message . "\n";
        echo $message . "\n";
    }

    private function runCommand($command) {
        $this->log("Executing: " . $command);
        exec($command . " 2>&1", $outputArray, $returnValue);
        $commandOutput = implode("\n", $outputArray);
        $this->log("Output: " . $commandOutput);
        $this->log("Return value: " . $returnValue);
        return $returnValue === 0;
    }

    private function setPermissions() {
        $dirs = [
            'storage' => 0755,
            'storage/app' => 0755,
            'storage/app/public' => 0755,
            'storage/framework' => 0755,
            'storage/framework/cache' => 0755,
            'storage/framework/sessions' => 0755,
            'storage/framework/views' => 0755,
            'storage/logs' => 0755,
            'bootstrap/cache' => 0755,
            'public' => 0755
        ];

        foreach ($dirs as $dir => $permission) {
            $path = $this->basePath . '/' . $dir;
            if (file_exists($path)) {
                chmod($path, $permission);
                $this->log("Set permissions {$permission} on: {$dir}");
            } else {
                $this->log("Warning: Directory not found: {$dir}");
            }
        }
    }

    public function deploy() {
        try {
            // Ensure we're in the right directory
            chdir($this->basePath);

            // Clear any existing optimizations
            $this->runCommand('php artisan clear-compiled');
            $this->runCommand('php artisan cache:clear');
            $this->runCommand('php artisan config:clear');
            $this->runCommand('php artisan view:clear');
            $this->runCommand('php artisan route:clear');

            // Install dependencies and optimize
            $this->runCommand('composer install --no-dev --optimize-autoloader');
            
            // Set up storage
            if (!file_exists($this->basePath . '/public/storage')) {
                $this->runCommand('php artisan storage:link');
            }

            // Set proper permissions
            $this->setPermissions();

            // Generate caches
            $this->runCommand('php artisan config:cache');
            $this->runCommand('php artisan route:cache');
            $this->runCommand('php artisan view:cache');
            $this->runCommand('php artisan optimize');

            // Update .env for production
            if (file_exists($this->basePath . '/.env')) {
                $env = file_get_contents($this->basePath . '/.env');
                $env = preg_replace('/APP_ENV=.*/', 'APP_ENV=production', $env);
                $env = preg_replace('/APP_DEBUG=.*/', 'APP_DEBUG=false', $env);
                file_put_contents($this->basePath . '/.env', $env);
                $this->log("Updated .env for production");
            }

            $this->log("Deployment completed successfully!");
            return true;

        } catch (Exception $e) {
            $this->log("Error during deployment: " . $e->getMessage());
            return false;
        } finally {
            // Save deployment log
            $logPath = $this->basePath . '/storage/logs/deploy.log';
            file_put_contents($logPath, $this->output, FILE_APPEND);
        }
    }
}

// Run deployment
$deployer = new Deployer();
$deployer->deploy();