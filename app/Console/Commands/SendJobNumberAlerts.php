<?php

namespace App\Console\Commands;

use App\Mail\JobNumberAlertEmail;
use App\Models\Category;
use App\Models\Job;
use App\Models\JobNumberAlert;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Mail;

class SendJobNumberAlerts extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'command:send-job-number-alerts  {--force}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sends job number alerts';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $alerts = JobNumberAlert::all();

        foreach ($alerts as $alert) {
            $from = Carbon::today(new \DateTimeZone('America/Toronto'));
            $to = Carbon::today(new \DateTimeZone('America/Toronto'))->addDays($alert->days - 1);
            $from = $from->format('Y-m-d');
            $to = $to->format('Y-m-d');

            $scheduledQuery = Job::query();
            $unscheduledQuery = Job::query();
            $category = null;

            if ($alert->category_id) {
                $scheduledQuery->where('category_id', $alert->category_id);
                $unscheduledQuery->where('category_id', $alert->category_id);
                $category = Category::whereId($alert->category_id)->select(['id', 'name'])->first()->name;
            }

            $scheduledQuery
                ->whereNotNull('start_date')
                ->whereDate('start_date', '>=', $from)
                ->whereDate('start_date', '<=', $to);

            $scheduledCount = $scheduledQuery->count();
            $unscheduledCount = $unscheduledQuery->whereNull('start_date')->count();

            $condition = false;
            if ($alert->condition == 'above') {
                if ($scheduledCount >= ($alert->no_of_jobs + 1)) {
                    $condition = true;
                }
            } else if ($alert->condition == 'below') {
                if ($scheduledCount == ($alert->no_of_jobs - 1)) {
                    $condition = true;
                }
            }
            $this->info('scheduled: ' . $scheduledCount . ' unscheduled: ' . $unscheduledCount);
            if ($this->canSendAlert($alert) && $condition) {
                $this->sendAlert($alert, $scheduledCount, $unscheduledCount, $category);
            }

        }
        return 0;
    }

    public function canSendAlert($alert): bool
    {
        $key = 'canSendAlert-' . $alert->id . $alert->condition;
        $value = Cache::store('redis')->get($key);
        if ($this->option('force')) {
            return true;
        }
        return !$value;
    }

    public function sendAlert($alert, $scheduledCount, $unscheduledCount, $category)
    {
        $key = 'canSendAlert-' . $alert->id . $alert->condition;

        $emails = $alert->receivers['emails'];
        if ($emails) {
            foreach ($emails as $email) {
                error_log($email);
                $subject = 'Emergency amount of jobs ' . $alert->condition . ' ' . $alert->no_of_jobs . ' for the next ' . $alert->days . ' days';
                Mail::to($email)->send(new JobNumberAlertEmail($subject, $scheduledCount, $unscheduledCount, $category));
            }
        }

        $alert->last_alert = now();
        $alert->save();
        Cache::store('redis')->set($key, true, now()->addHours(12));
    }
}
