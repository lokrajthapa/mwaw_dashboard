<?php

namespace App\Servicefusion;

use App\Allfix\UpdatesNotifier;
use App\Models\Job;

class JobSyncer
{
    static public function sync($parsedJob)
    {
        $assignedTechs = $parsedJob['assignedTechs'];
        $job = Job::query()->where('sf_id', $parsedJob['sf_id'])->first();
        if (!$job) {
            $job = Job::query()->create(JobSyncer::unsetValues($parsedJob));
        } else {

            //todo check for sub_category for allfix
            $notifier = new UpdatesNotifier();
            $updates = $notifier->generateUpdate($job, $parsedJob);
            if ($updates && $job->po) {
                $notifier->dispatchSms($job, $updates);
            }

            $job->update(JobSyncer::unsetValues($parsedJob));
        }
        error_log($job->id . ' ' . $parsedJob['sf_id']);
        $job->technicians()->sync($assignedTechs);
    }

    static public function unsetValues($array)
    {
        unset($array['assignedTechs']);
        unset($array['customFields']);
        unset($array['services']);
        unset($array['payments']);
        return $array;
    }
}
