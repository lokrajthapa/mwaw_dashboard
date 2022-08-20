<?php

namespace App\Allfix;

use App\Jobs\SendSmsJob;
use App\Models\Sms;
use App\Models\Status;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class UpdatesNotifier
{
    public function generateUpdate(Model $job, $parsedJob): bool|string
    {
        $job->load(['status']);
        $status = $job->status->name;

        $parsedJobStatus = Status::query()->where('id', $parsedJob['status_id'])->first()->name;

        if ($status == 'New' && !$job->start_date && $parsedJob['start_date']) {
            $scheduledDate = new Carbon($parsedJob['start_date'] . ' ' . $parsedJob['start_time']);
            return 'Booked On: ' . $scheduledDate->format('Y-m-d h:i A');

        } elseif ($status != 'Need to order part' && $parsedJobStatus == 'Need to order part') {
            $parsedPartsInfo = $this->parsePartsInfo($parsedJob);
            $partNumbers = [];
            foreach ($parsedPartsInfo as $item) {
                $partNumbers[] = $item['partNumber'];
            }
            $partNumbersString = implode(', ', $partNumbers);
            $partsAndLabourTotal = $this->getLabourAndPartsTotal($parsedJob);
            $totalPaymentReceived = $this->getPaymentAmount($parsedJob);
            $collectedPercent = $partsAndLabourTotal != 0 ? round($totalPaymentReceived / $partsAndLabourTotal * 100, 2) : 0;
            $update = "Job sold for $$partsAndLabourTotal\n";
            $update .= "PARTS:\n";
            $update .= $partNumbersString . PHP_EOL;
            $update .= $collectedPercent . '% was collected';
            return $update;

        } elseif ($status != 'Parts ordered' && $parsedJobStatus == 'Parts ordered') {
            return 'PARTS ORDERED';

        } elseif ($status != 'Return visit' && $parsedJobStatus == 'Return visit') {
            $scheduledDate = new Carbon($parsedJob['start_date'] . ' ' . $parsedJob['start_time']);
            return 'Return visit booked on: ' . $scheduledDate->format('Y-m-d h:i A');
        } elseif (($status != 'Completed' && $status != 'Invoiced') && ($parsedJobStatus == 'Completed' || $parsedJobStatus == 'Invoiced')) {
            return "Job is completed.\nCollected with " . $this->getPaymentMethods($parsedJob);
        }

        return false;
    }

    public function dispatchSms(Model $job, $update): void
    {
        $sms = Sms::query()->whereNotNull('job_id')->where('type', 'job')
            ->where('job_id', $job->po)->orderByDesc('updated_at')->first();
        if ($sms) {
            $body = $sms->body;
            $body .= PHP_EOL . '++++++++++++++++++' . PHP_EOL;
            $body .= $update;
            $sms->body = $body;
            $sms->save();
            SendSmsJob::dispatch($sms->to, $sms->from, $body);
        }
    }

    public function getPartsInfo($parsedJob)
    {
        foreach ($parsedJob['customFields'] as $customField) {
            if ($customField['name'] == 'Part info') {
                return $customField['value'];
            }
        }
        return '';
    }

    public function getLabourAndPartsTotal($parsedJob): float
    {
        $total = 0;
        foreach ($parsedJob['services'] as $service) {
            $total += $service['total'];
            $taxPercent = $service['tax'] == 'HST' ? 13 : 5;
            $tax = $taxPercent / 100 * $service['total'];
            $total += $tax;
        }
        return round($total, 2);
    }

    public function getPaymentAmount($parsedJob)
    {
        $total = 0;
        foreach ($parsedJob['payments'] as $payment) {
            $total += $payment['amount'];
        }
        return $total;
    }

    public function getPaymentMethods($parsedJob): string
    {
        $paymentMethods = [];
        foreach ($parsedJob['payments'] as $payment) {
            $paymentMethods[] = $payment['type'];
        }
        return implode(', ', $paymentMethods);
    }

    public function parsePartsInfo($parsedJob): array
    {
        $partsInfo = $this->getPartsInfo($parsedJob);
        if ($partsInfo) {
            $partsInfoData = str_replace("\n", '', $partsInfo);
            $partsInfoArray = explode("\r", $partsInfoData);
            $normalizedPartsInfoArray = [];
            foreach ($partsInfoArray as $item) {
                $item = trim($item);
                if ($item) {
                    $normalizedPartsInfoArray[] = $item;
                }
            }
            $chunkedInfo = array_chunk($normalizedPartsInfoArray, 6);
            $parsedInfo = [];
            foreach ($chunkedInfo as $item) {
                $partNameString = substr($item[0], strpos($item[0], 'Part name'));
                $partName = $this->normalize($partNameString, 'Part name:');
                if ($partName) {
                    $parsedInfo[] = [
                        'partName' => $partName,
                        'partNumber' => $this->normalize($item[1], str_contains($item[1], 'Part number:') ? 'Part number:' : 'Part number'),
                    ];
                }
            }
            return $parsedInfo;
        }
        return [];
    }

    public function normalize($string, $removeString = null): string
    {
        if ($removeString) {
            return trim(str_replace($removeString, '', $string));
        }
        return trim($string);
    }
}
