<?php

namespace App\Console\Commands;

use App\Models\Job;
use App\Servicefusion\JobParser;
use App\Servicefusion\JobSyncer;
use App\Servicefusion\Servicefusion;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class SyncJobs extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'command:sync-jobs {--all}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sync service fusion jobs';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $servicefusion = new Servicefusion();

        if ($this->option('all')) {
            $jobs = $servicefusion->getAllJobs();

        } else {
            $jobs = $servicefusion->getLatestJobs(app()->environment('production') ? 20 : 5);
        }
        foreach ($jobs as $job) {
            $parsedJob = JobParser::parse($job);
            try {
                JobSyncer::sync($parsedJob);
            } catch (\Exception $e) {
                error_log($e->getMessage());
            }
        }
        return 0;
    }

    private function sendUpdates($poNumber, $update)
    {

    }
}
