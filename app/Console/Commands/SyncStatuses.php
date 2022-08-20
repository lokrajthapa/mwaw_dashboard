<?php

namespace App\Console\Commands;

use App\Models\Status;
use App\Servicefusion\Servicefusion;
use Illuminate\Console\Command;

class SyncStatuses extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'command:sync-statuses';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sync service fusion statuses';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $servicefusion = new Servicefusion();
        $statuses = $servicefusion->getAllStatuses();
        foreach ($statuses as $status) {
            $status['sf_id'] = $status['id'];
            unset($status['code']);
            unset($status['is_custom']);
            unset($status['code']);
            Status::query()
                ->updateOrCreate(['sf_id' => $status['sf_id']], $status);
        }
        return 0;
    }
}
