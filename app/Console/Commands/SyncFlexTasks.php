<?php

namespace App\Console\Commands;

use App\Flex\FlexTaskParser;
use App\Models\FlexCall;
use App\Models\FlexTask;
use Illuminate\Console\Command;
use Twilio\Rest\Client;

class SyncFlexTasks extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'command:sync-flex-tasks {--all}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sync tasks from flex';

    /**
     * Execute the console command.
     *
     * @return int
     * @throws \Twilio\Exceptions\ConfigurationException
     */
    public function handle()
    {
        $client = new Client(env('TWILIO_FLEX_ACCOUNT_SID'), env('TWILIO_FLEX_AUTH_TOKEN'));
        if ($this->option('all')) {
            $tasks = $client->taskrouter->workspaces(env('TWILIO_FLEX_WORKSPACE_SID'))->tasks->read();
        } else {
            $tasks = $client->taskrouter->workspaces(env('TWILIO_FLEX_WORKSPACE_SID'))->tasks->read([], 100);
        }
        $this->info('count: ' . count($tasks));
        foreach ($tasks as $task) {
            FlexTask::query()->updateOrCreate(['sid' => $task->sid], FlexTaskParser::parse($task));
        }
        return 0;
    }
}
