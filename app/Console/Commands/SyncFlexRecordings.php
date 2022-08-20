<?php

namespace App\Console\Commands;

use App\Flex\FlexRecordingParser;
use App\Models\FlexRecording;
use Illuminate\Console\Command;
use Twilio\Rest\Client;

class SyncFlexRecordings extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'command:sync-flex-recordings {--all}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sync flex recordings';

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
            $recordings = $client->recordings->read();

        } else {
            $recordings = $client->recordings->read([], 100);
        }

        foreach ($recordings as $recording) {
            $this->info($recording->source);
            FlexRecording::query()->updateOrCreate(['sid' => $recording->sid], FLexRecordingParser::parse($recording));
        }
        return 0;
    }
}
