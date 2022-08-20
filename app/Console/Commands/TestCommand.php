<?php

namespace App\Console\Commands;

use App\Allfix\UpdatesNotifier;
use App\Fordpass\FordpassVehicle;
use App\Google\GmailParser;
use App\Google\Google;
use App\Models\Job;
use App\Models\User;
use App\Notifications\TestNotification;
use App\Servicefusion\JobParser;
use App\Servicefusion\JobSyncer;
use App\Servicefusion\Servicefusion;
use Google\Service\Gmail;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Notification;

class TestCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'command:test {--fresh} {--sync}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Code playground';

    /**
     * Execute the console command.
     *
     * @return int
     * @throws \Exception
     */
    public function handle()
    {
        return 0;
    }
}
