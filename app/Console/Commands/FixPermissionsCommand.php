<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class FixPermissionsCommand extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'deploy:fix-permissions {--force : Run even if the command executed recently}';

    /**
     * The console command description.
     */
    protected $description = 'Normalize permissions for writable directories after a deployment';

    /**
     * Log file used to throttle repeated executions.
     */
    private const MARKER_FILE = 'logs/last_permission_fix.timestamp';

    /**
     * Directories to normalize, relative to the project base path.
     */
    private const TARGET_DIRECTORIES = [
        'storage',
        'bootstrap/cache',
    ];

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        if ($this->shouldSkipBecauseOfThrottle()) {
            $this->info('Permission fix skipped (recently executed). Use --force to override.');
            return self::SUCCESS;
        }

        foreach (self::TARGET_DIRECTORIES as $relativePath) {
            $absolutePath = base_path($relativePath);

            if (! File::exists($absolutePath)) {
                $this->warn("Skipping missing path: {$relativePath}");
                continue;
            }

            $this->info("Normalizing permissions for {$relativePath}");
            $this->applyPermissions($absolutePath);
        }

        $this->storeThrottleMarker();

        $this->info('Permission normalization completed.');

        return self::SUCCESS;
    }

    /**
     * Decide whether the command should be throttled.
     */
    private function shouldSkipBecauseOfThrottle(): bool
    {
        if ($this->option('force')) {
            return false;
        }

        $markerPath = storage_path(self::MARKER_FILE);

        if (! File::exists($markerPath)) {
            return false;
        }

        $lastRunTimestamp = File::lastModified($markerPath);

        // Skip if the command ran within the last five minutes.
        return $lastRunTimestamp !== false && $lastRunTimestamp >= now()->subMinutes(5)->getTimestamp();
    }

    /**
     * Store the marker file indicating the last execution time.
     */
    private function storeThrottleMarker(): void
    {
        $markerPath = storage_path(self::MARKER_FILE);
        $directory = dirname($markerPath);

        if (! File::exists($directory)) {
            File::makeDirectory($directory, 0775, true);
        }

        File::put($markerPath, now()->toIso8601String());
        File::chmod($markerPath, 0664);
    }

    /**
     * Recursively apply permissions to a directory and its children.
     */
    private function applyPermissions(string $absolutePath): void
    {
        if (File::isDirectory($absolutePath)) {
            File::chmod($absolutePath, 0775);

            foreach (File::directories($absolutePath) as $directory) {
                $this->applyPermissions($directory);
            }

            foreach (File::files($absolutePath) as $file) {
                File::chmod($file->getPathname(), 0664);
            }

            return;
        }

        // Handle the case where the target is a file rather than a directory.
        File::chmod($absolutePath, 0664);
    }
}