<?php

class Deployer {
    private $output = '';
    private $basePath;
    private $envProductionPath;
    private $envTemplate = '.env.example';
    
    public function __construct() {
        $this->basePath = dirname(__FILE__);
        // Automatically detect Hostinger environment and set paths
        $this->envProductionPath = $this->detectEnvPath();
        $this->log("Deployment started at " . date('Y-m-d H:i:s'));
    }

    private function detectEnvPath() {
        // Try to detect Hostinger's environment
        $possiblePaths = [
            '/home/u123456789/domains/mcc-pes.com/public_html/MCC-IPES/.env',  // Adjust domain
            $this->basePath . '/.env.production',
            $this->basePath . '/.env'
        ];

        foreach ($possiblePaths as $path) {
            if (file_exists($path)) {
                $this->log("Found environment file at: " . $path);
                return $path;
            }
        }

        $this->log("Warning: No existing environment file found");
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
        
        // If we have an existing .env file, create a backup
        if (file_exists($envPath)) {
            $this->log("Backing up existing .env file...");
            copy($envPath, $envPath . '.backup.' . date('Y-m-d-His'));
        }
        
        // If we have our production .env, use it
        if (file_exists($this->envProductionPath) && $this->envProductionPath !== $envPath) {
            $this->log("Restoring production .env file from: " . $this->envProductionPath);
            copy($this->envProductionPath, $envPath);
            chmod($envPath, 0600); // Secure the env file
            return true;
        }
        
        // If we have a template, create a new .env
        if (file_exists($this->basePath . '/' . $this->envTemplate)) {
            $this->log("Creating new .env file from template...");
            copy($this->basePath . '/' . $this->envTemplate, $envPath);
            chmod($envPath, 0600);
            return true;
        }
        
        $this->log("Warning: Could not find any environment file to use");
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
            
            $this->log("Starting deployment in: " . $this->basePath);

            // Create storage directory if it doesn't exist
            if (!file_exists($this->basePath . '/storage')) {
                mkdir($this->basePath . '/storage', 0755, true);
            }

            // Preserve environment file before any operations
            if (!$this->preserveEnvironmentFile()) {
                $this->log("WARNING: Could not set up environment file!");
            }

            // Clear all caches first
            $this->log("Clearing all caches...");
            $clearCommands = [
                'php artisan clear-compiled',
                'php artisan cache:clear',
                'php artisan config:clear',
                'php artisan view:clear',
                'php artisan route:clear'
            ];

            foreach ($clearCommands as $command) {
                $this->runCommand($command);
            }

            // Install dependencies
            $this->log("Installing dependencies...");
            $this->runCommand('composer install --no-dev --optimize-autoloader');
            
            // Set up storage link
            if (!file_exists($this->basePath . '/public/storage')) {
                $this->log("Creating storage link...");
                $this->runCommand('php artisan storage:link');
            }

            // Set proper permissions
            $this->setPermissions();

            // Test database connection and handle environment
            $dbConnected = $this->testDatabaseConnection();
            
            if ($dbConnected) {
                $this->log("Database connection successful, generating caches...");
                
                // Run migrations if needed
                if ($this->shouldRunMigrations()) {
                    $this->runCommand('php artisan migrate --force');
                }

                // Generate caches
                $this->runCommand('php artisan config:cache');
                $this->runCommand('php artisan route:cache');
                $this->runCommand('php artisan view:cache');
                $this->runCommand('php artisan optimize');
            } else {
                $this->log("WARNING: Database connection failed! Please check your .env configuration");
                $this->log("Skipping migrations and cache generation...");
            }

            $this->log("Deployment completed successfully!");
            return true;

        } catch (Exception $e) {
            $this->log("ERROR during deployment: " . $e->getMessage());
            return false;
        } finally {
            // Save deployment log
            $logPath = $this->basePath . '/storage/logs/deploy.log';
            file_put_contents($logPath, $this->output, FILE_APPEND);
            
            // Output final status
            $this->log("Deployment process finished. Check deploy.log for details.");
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