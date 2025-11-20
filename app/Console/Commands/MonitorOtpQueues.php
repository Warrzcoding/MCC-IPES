<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Facades\Mail;

class MonitorOtpQueues extends Command
{
    protected $signature = 'otp:monitor {--alert : Send alert notifications}';
    protected $description = 'Monitor OTP email queues and delivery status';

    public function handle()
    {
        $this->info('Monitoring OTP Email Queues...');

        // Check Redis queue status
        $queueSize = Redis::llen('queues:default');
        $this->info("Current queue size: {$queueSize}");

        // Check failed jobs
        $failedJobs = \DB::table('failed_jobs')->where('queue', 'default')->count();
        $this->info("Failed jobs count: {$failedJobs}");

        // Check recent email delivery logs
        $recentErrors = Log::getLogger()->getHandlers();
        // This is a simplified check - in production you'd parse log files

        $issues = [];

        if ($queueSize > 100) {
            $issues[] = "Queue size is high: {$queueSize} jobs";
        }

        if ($failedJobs > 10) {
            $issues[] = "High number of failed jobs: {$failedJobs}";
        }

        if (!empty($issues)) {
            $message = "OTP Email Queue Issues:\n" . implode("\n", $issues);

            Log::warning($message);

            if ($this->option('alert')) {
                // Send alert email to administrators
                try {
                    Mail::raw($message, function ($mail) {
                        $mail->to('admin@mccpes.com')
                             ->subject('OTP Email Queue Alert');
                    });
                    $this->info('Alert email sent to administrators');
                } catch (\Exception $e) {
                    $this->error('Failed to send alert email: ' . $e->getMessage());
                }
            }

            $this->error('Issues found:');
            foreach ($issues as $issue) {
                $this->error("- {$issue}");
            }
        } else {
            $this->info('All systems operational');
        }

        return 0;
    }
}