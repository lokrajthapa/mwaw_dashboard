<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;
use Twilio\Rest\Client;

class SyncFlexAgents extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'command:sync-flex-agents';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sync agents from flex';

    /**
     * Execute the console command.
     *
     * @return int
     * @throws \Twilio\Exceptions\ConfigurationException
     */
    public function handle(): int
    {
        $client = new Client(env('TWILIO_FLEX_ACCOUNT_SID'), env('TWILIO_FLEX_AUTH_TOKEN'));

        $workers = $client->taskrouter->v1->workspaces(env('TWILIO_FLEX_WORKSPACE_SID'))->workers->read();
        foreach ($workers as $worker) {
            $attributes = json_decode($worker->attributes, true);
            $user = User::query()
                ->where('email', $attributes['email'])->first();

            echo $worker->attributes . PHP_EOL;
            if (!$user) {
                User::query()->create([
                    'name' => $attributes['full_name'],
                    'role' => 'agent',
                    'email' => $attributes['email'],
                    'agent_id' => $attributes['contact_uri'],
                    'password' => Hash::make('password')
                ]);
            } else {
                $user->update([
                    'name' => $attributes['full_name'],
                    'role' => 'agent',
                    'agent_id' => $attributes['contact_uri'],
                ]);
            }
        }

        return 0;
    }
}
