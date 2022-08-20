<?php

namespace App\Console\Commands;

use App\Flex\FlexCallParser;
use App\Models\FlexCall;
use Illuminate\Console\Command;
use Twilio\Rest\Client;

class SyncFlexCalls extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'command:sync-flex-calls {--all}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sync calls from flex';

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
            $calls = $client->calls->read();
        } else {
            $calls = $client->calls->read([], 100);
        }
        foreach ($calls as $call) {
            $this->info($call->sid);
            FlexCall::query()->updateOrCreate(['sid' => $call->sid], FlexCallParser::parse($call));
        }
        return 0;
    }
}
