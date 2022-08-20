<?php

namespace App\Models;

use App\Mail\SmsForwarderEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Mail;

class Sms extends Model
{
    use HasFactory;

    protected $fillable = ['job_id', 'from', 'to', 'body', 'type', 'data', 'email_sent', 'email_id'];

    protected $casts = [
        'data' => 'array',
        'email_sent' => 'boolean'
    ];

    public function sendEmail($emergency = false)
    {
        $parsedSms = $this->parsedSms();
        $references = $this->getEmailReferences();
        Mail::to([['email' => 'jobs@manwithawrench.com', 'name' => 'Nati Jobs dispatch']])
            ->queue(new SmsForwarderEmail($this->id, $parsedSms, $this->mapsLink(), $this->mapsImageUrl(), $references, $emergency));
    }

    public function getEmailReferences(): array
    {
        $emailIds = Sms::query()->where('job_id', $this->job_id)
            ->whereNotNull('email_id')
            ->whereNot('id', $this->id)
            ->select('email_id')->orderByDesc('created_at')->get();
        return $emailIds->pluck('email_id')->toArray();
    }

    public function sendEmergencyEmail()
    {
        $this->sendEmail(true);
    }

    /**
     * @throws \Twilio\Exceptions\ConfigurationException
     * @throws \Twilio\Exceptions\TwilioException
     */
    public function notifyEmergencyNumbers()
    {
        $parsedSms = $this->parsedSms();
        $recipients = [
            '+14165873466',
            '+14164571282',
            '+14372425744',
//            '+14168717173',//dean
        ];
        $sender = '+12898149933';
        $body = 'ALLFIX - EMERGENCY - ' . $parsedSms['address']['value'];
        foreach ($recipients as $recipient) {
            sendSms($sender, $recipient, $body);
        }
    }

    public function parsedSms(): array
    {
        if (str_contains($this->body, 'Service appointment')) {
            return $this->parsedServiceAppointmentSms();

        } elseif ($this->isRawSms()) {
            return [
                'type' => 'raw',
                'body' => ['title' => 'Content', 'value' => $this->body],
                'updates' => ['title' => 'Updates', 'value' => []],
            ];
        }

        $splitData = explode(PHP_EOL, $this->body);
        $count = count($splitData);
        $jobId = $this->normalize($splitData[1], 'JOB ID#');
        $name = $this->normalize($splitData[2], 'Name:');
        $phone = $this->normalize($splitData[3], 'Phone:');
        $address = $this->normalize($splitData[4], 'Address:');
        $jobType = $this->normalize($splitData[5], 'JobType:');
        $notes = $this->normalize($splitData[6], 'Notes:');

        $i = 7;
        while ($i < $count - 1 && (!str_contains($splitData[$i], '+++')) && (!str_contains($splitData[$i], '---'))) {
            if (!str_contains($splitData[$i], 's1j.co')) {
                $notes .= PHP_EOL . $splitData[$i];
            }
            $i++;
        }

        $link = '';
        foreach ($splitData as $datum) {
            if (str_contains($datum, 's1j.co')) {
                $link = $this->normalize($datum);
            }
        }

        $hasUpdate = str_contains($this->body, '+++') || str_contains($this->body, '---');
        $updates = [];
        if ($hasUpdate) {
            $update = '';
            foreach ($splitData as $item) {
                if (str_contains($item, '+++') || str_contains($item, '---')) {
                    $updates[] = $update;
                    $update = '';
                } else {
                    $update .= ($update ? PHP_EOL : '') . $item;
                }
            }

            if ($update) {
                $updates[] = $update;
            }

            array_shift($updates);
        }

        return [
            'type' => 'job',
            'jobId' => ['title' => 'JOB ID#', 'value' => $jobId],
            'name' => ['title' => 'Name', 'value' => $name],
            'phone' => ['title' => 'Phone', 'value' => $phone],
            'address' => ['title' => 'Address', 'value' => $address],
            'jobType' => ['title' => 'Job Type', 'value' => $jobType],
            'notes' => ['title' => 'Notes', 'value' => $notes],
            'link' => ['title' => 'Link', 'value' => $link],
            'updates' => ['title' => 'Updates', 'value' => $updates],
        ];
    }

    public function isRawSms(): bool
    {
        $body = $this->body;
        return (!str_contains($body, 'Name')) && (!str_contains($body, 'Phone')) && (!str_contains($body, 'Address'));
    }

    public function parsedServiceAppointmentSms(): array
    {
        $splitData = explode(PHP_EOL, $this->body);
        $jobId = $this->normalize($this->findLine($splitData, 'Job ID:'), 'Job ID:');
        $date = $this->normalize($this->findLine($splitData, 'When:'), 'When:');
        $address = $this->normalize($this->findLine($splitData, 'Where:'), 'Where:');
        $jobType = $this->normalize($this->findLine($splitData, 'Job type:'), 'Job type:');
        $lastLine = $splitData[count($splitData) - 1];
        $phone = '';
        if (!str_contains($lastLine, 'Job type:')) {
            $phone = $this->normalize($lastLine);
        }
        return [
            'type' => 'reminder',
            'jobId' => ['title' => 'Job ID', 'value' => $jobId],
            'date' => ['title' => 'When', 'value' => $date],
            'address' => ['title' => 'Where', 'value' => $address],
            'jobType' => ['title' => 'Job Type', 'value' => $jobType],
            'phone' => ['title' => 'Phone', 'value' => $phone],
            'updates' => ['title' => 'Updates', 'value' => []],
        ];
    }

    public function findLine($array, $needle): string
    {
        foreach ($array as $item) {
            if (str_contains($item, $needle)) {
                return $item;
            }
        }
        return '';
    }

    public function mapsLink(): string
    {
        $parsedSms = $this->parsedSms();
        if (key_exists('address', $parsedSms)) {
            $searchQuery = $parsedSms['address']['value'];
            return 'https://www.google.com/maps/search/?api=1&' . http_build_query(['query' => $searchQuery]);
        }

        return '';
    }

    public function mapsImageUrl(): string
    {
        $parsedSms = $this->parsedSms();
        if (key_exists('address', $parsedSms)) {
            $url = 'https://maps.googleapis.com/maps/api/staticmap?';
            $location = $parsedSms['address']['value'];
            $query = http_build_query([
                'center' => $location,
                'zoom' => 13,
                'size' => '600x300',
                'maptype' => 'roadmap',
                'markers' => 'color:red|label:L|' . $location,
                'key' => env('MAPS_API_KEY')
            ]);
            return $url . $query;
        }
        return '';
    }

    public function normalize($string, $removeString = null): string
    {
        if ($removeString) {
            return trim(str_replace($removeString, '', $string));
        }
        return trim($string);
    }
}
