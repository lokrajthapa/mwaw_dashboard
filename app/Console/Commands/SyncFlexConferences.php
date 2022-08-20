<?php

namespace App\Console\Commands;

use App\Models\FlexConference;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Twilio\Rest\Client;

class SyncFlexConferences extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'command:sync-flex-conferences {--all}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sync conferences from flex';

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
            $conferences = $client->conferences->read();
        } else {
            $conferences = $client->conferences->read([], 100);
        }
        foreach ($conferences as $conference) {
            $this->info($conference->sid);

            FlexConference::query()->updateOrCreate(['sid' => $conference->sid],
                ['sid' => $conference->sid, 'created_at' => new Carbon($conference->dateCreated)]
            );
            $this->info($conference->dateCreated->format('Y-m-d h:i:s'));
        }
        return 0;
    }
}
