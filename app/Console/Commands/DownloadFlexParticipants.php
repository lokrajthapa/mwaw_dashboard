<?php

namespace App\Console\Commands;

use App\Models\FlexCall;
use App\Models\FlexConference;
use Illuminate\Console\Command;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\Process\Process;
use Twilio\Exceptions\TwilioException;
use Twilio\Rest\Client;

class DownloadFlexParticipants extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'command:download-flex-participants {startDate} {endDate}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Download flex participants history';

    /**
     * Execute the console command.
     *
     * @return int
     * @throws TwilioException
     */
    public function handle()
    {
        $client = new Client(env('TWILIO_FLEX_ACCOUNT_SID'), env('TWILIO_FLEX_AUTH_TOKEN'));
        $start = new Carbon($this->argument('startDate'));
        $end = new Carbon($this->argument('endDate'));

        $conferenceIdsBySid = FlexConference::query()->select(['id', 'sid'])
            ->orderByDesc('created_at')->limit(500)->get()
            ->mapWithKeys(function ($item) {
                return [$item->sid => $item->id];
            });

        $callsBySid = FlexCall::query()->whereNull('conference_id')->get()
            ->mapWithKeys(function ($item) {
                return [$item->sid => $item];
            });

        $days = collect($client->bulkexports->v1->exports('Participants')
            ->days->read());
        $validDays = $days->filter(function ($item) {
            return $item->size != 1;
        })->map(function ($item) {
            return $item->day;
        })->values()->toArray();

        while ($start->lessThanOrEqualTo($end)) {
            try {
                $currentDay = $start->format('Y-m-d');
                if (in_array($currentDay, $validDays)) {
                    $this->info($currentDay);
                    $day = $client->bulkexports->v1->exports('Participants')
                        ->days($currentDay)->fetch();

                    $urlPath = parse_url($day->redirectTo, PHP_URL_PATH);
                    $filename = basename($urlPath);

                    $file = file_get_contents($day->redirectTo);
                    Storage::disk('public')->put('temp/' . $filename, $file);
                    $outputFile = $this->unzip(storage_path('app/public/temp/'), $filename);

                    $file = fopen($outputFile, 'r');

                    while (!feof($file)) {
                        $line = fgets($file);
                        $record = json_decode($line, true);

                        if ($record) {
                            $conferenceSid = $record['conference_sid'];
                            $callSid = $record['call_sid'];

                            if ($conferenceIdsBySid->has($conferenceSid) && $callsBySid->has($callSid)) {
                                $callsBySid[$callSid]->update(['conference_id' => $conferenceIdsBySid[$conferenceSid]]);
                            }
                        }
                    }
                    fclose($file);
                    unlink($outputFile);

                    $this->info($filename);
                }

                $start->addDay();

            } catch (\Exception $e) {
                $this->error($e->getMessage());
            }

        }
        return 0;
    }

    /**
     * @throws \Exception
     */
    public function unzip($directory, $filename)
    {
        $process = new Process(['/usr/bin/gzip', '-d', '-f', $directory . $filename]);
        $process->run();
        if ($process->isSuccessful()) {
            return $directory . str_replace('.gz', '', $filename);
        }

        throw new \Exception($process->getErrorOutput());
    }
}
