<?php

namespace App\Console\Commands;

use App\Models\Sms;
use Illuminate\Console\Command;

class UpdateSmsJobId extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'command:update-sms-job-id';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Updates existing sms job id';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        Sms::query()
            ->chunk(100, function ($sms) {
                foreach ($sms as $item) {
                    try {
                        $parsedSms = $item->parsedSms();
                        $item->job_id = $parsedSms['jobId']['value'] ?: null;
                        $item->type = $parsedSms['type'] ?: null;
                    } catch (\Exception $e) {
                    }
                    $item->save();
                }
            });
        return 0;
    }
}
