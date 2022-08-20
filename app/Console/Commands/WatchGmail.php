<?php

namespace App\Console\Commands;

use App\Google\Google;
use Exception;
use Illuminate\Console\Command;
use Google\Service\Gmail;

class WatchGmail extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'command:watch-gmail';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Refreshes gmail webhook';

    /**
     * Execute the console command.
     *
     * @return int
     * @throws Exception
     */
    public function handle()
    {
        $client = Google::getClient();
        $service = new Gmail($client);
        $watchRequest = new Gmail\WatchRequest();
        $watchRequest->labelIds = ['INBOX'];
        $watchRequest->topicName = 'projects/precise-slice-355807/topics/gmail-webhook';
        $resp = $service->users->watch('me',$watchRequest);
        $this->info(json_encode($resp));
        return 0;
    }
}
