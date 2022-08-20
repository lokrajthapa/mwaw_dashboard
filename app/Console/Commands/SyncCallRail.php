<?php

namespace App\Console\Commands;

use App\CallRail\CallRail;
use App\Models\Lead;
use Carbon\Carbon;
use Illuminate\Console\Command;

class SyncCallRail extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'command:sync-call-rail {--all}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sync leads from call rail';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $callRail = new CallRail();
        if ($this->option('all')) {
            $calls = $callRail->getAllCalls();
        } else {
            $calls = $callRail->getLatestCalls();
        }

        $calls = collect($calls);
        $calls = $calls->filter(function ($item) {
            return $item['gclid'] != null;
        });

        $callsByGclid = $calls->groupBy('gclid');

        foreach ($callsByGclid as $gclid => $data) {
            $this->info($gclid);
            $item = $data->pop();
            $this->info($item['start_time']);
            Lead::query()->firstOrCreate(['gclid' => $gclid], [
                'gclid' => $gclid,
                'source' => 'CallRail',
                'phone_no' => $item['tracking_phone_number'],
                'conversion_datetime' => (new Carbon($item['start_time']))->utc(),
            ]);
        }

        return 0;
    }
}
