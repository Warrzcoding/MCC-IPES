<?php

class Deployer {
    private $output = '';
    private $basePath;
    private $envBackupPath = '/home/YOUR_HOSTINGER_USERNAME/env_backup/.env.production';  // Adjust this path

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

    private function preserveEnvironmentFile() {
        $envPath = $this->basePath . '/.env';
        
        // If we have an existing .env file, back it up
        if (file_exists($envPath)) {
            $this->log("Backing up existing .env file...");
            copy($envPath, $envPath . '.backup');
        }
          
        // If we have a production env backup, restore it
        if (file_exists($this->envBackupPath)) {
            $this->log("Restoring production .env file...");
            copy($this->envBackupPath, $envPath);
            return true;
        }
        
        return false;
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
                mkdir($path, $permission, true);
                $this->log("Created directory with permissions {$permission}: {$dir}");
            }
        }

        // Ensure .env is protected
        if (file_exists($this->basePath . '/.env')) {
            chmod($this->basePath . '/.env', 0600);
            $this->log("Protected .env file permissions");
        }
    }

    public function deploy() {
        try {
            // Ensure we're in the right directory
            chdir($this->basePath);

            // Preserve environment file before any operations
            $this->preserveEnvironmentFile();

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

            // Verify database connection
            if ($this->testDatabaseConnection()) {
                // Generate caches only if database connection works
                $this->runCommand('php artisan config:cache');
                $this->runCommand('php artisan route:cache');
                $this->runCommand('php artisan view:cache');
                $this->runCommand('php artisan optimize');
            } else {
                $this->log("WARNING: Database connection failed, skipping cache generation");
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

    private function testDatabaseConnection() {
        try {
            if (!file_exists($this->basePath . '/.env')) {
                $this->log("No .env file found!");
                return false;
            }

            // Load .env file
            $env = file_get_contents($this->basePath . '/.env');
            if (preg_match('/DB_DATABASE=(.*)/', $env, $matches)) {
                if (empty(trim($matches[1]))) {
                    $this->log("Database name is empty in .env!");
                    return false;
                }
            }

            // Test connection using artisan
            $result = $this->runCommand('php artisan db:monitor');
            return $result;
        } catch (Exception $e) {
            $this->log("Database connection test failed: " . $e->getMessage());
            return false;
        }
    }
}

// Run deployment
$deployer = new Deployer();
$deployer->deploy();

// Run deployment
$deployer = new Deployer();
$deployer->deploy();