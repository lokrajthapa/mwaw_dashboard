<?php

namespace App\Console\Commands;

use App\Models\FlexConference;
use Illuminate\Console\Command;

class UpdateFlexConferenceDirection extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'command:update-conference-direction';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $conferences = FlexConference::query()
            ->whereNull('direction')
            ->whereHas('calls')
            ->with(['calls' => function ($q) {
                $q->where('from', 'like', '+%')
                    ->where('to', 'like', '+%');
            }])->get();
        foreach ($conferences as $conference) {
            if ($conference->calls->isNotEmpty()) {
                $this->info($conference->calls[0]->direction);
                $conference->update(['direction' => $conference->calls[0]->direction]);
            }
        }
        $this->info($conferences->count());
        return 0;
    }
}
