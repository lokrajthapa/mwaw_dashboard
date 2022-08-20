<?php

namespace App\Ivr;

use Carbon\Carbon;
use Twilio\TwiML\VoiceResponse;

class Greeting extends Base implements StateInterface
{

    public function execute($params = null): VoiceResponse
    {

        $response = new VoiceResponse();

        $response = $this->fixedHoliday($response);

        $say = $response->say('', $this->sayOptions());

        if ($this->isBusinessHour() || request()->input('From') == '+14386001481') {
            if (request()->input('To') != '+14752758174') {
                $say->prosody($this->speech('greeting'), $this->prosodyOptions());
            }
            $response->redirect($this->action(['mode' => 'mainSelection']));

        } else {
            $say->prosody($this->speech('greetingOffHour'), $this->prosodyOptions());
            $response->hangup();

        }

        return $response;
    }

    public function isBusinessHour(): bool
    {
        $now = Carbon::now(new \DateTimeZone('America/Toronto'));

        $weekend = $now->isSunday() || $now->isSaturday();
        $hour = $now->hour;

        return (!$weekend) && ($hour >= 9 && $hour <= 16);

    }

    public function fixedHoliday(VoiceResponse $response): VoiceResponse
    {
        $from = (new Carbon('2022-07-30 09:00:00', new \DateTimeZone('America/Toronto')));
        $to = (new Carbon('2022-08-02 06:00:00', new \DateTimeZone('America/Toronto')));

        $now = Carbon::now(new \DateTimeZone('America/Toronto'));

        if ($now->greaterThan($from) && $now->lessThanOrEqualTo($to)) {
            $say = $response->say('', $this->sayOptions());
            $say->prosody('
                We thank you for calling man with the wrench!
                Your everyday appliance repair plumbing HVAC and electrical solution specialists.

                Please note that the office will be closed for the long weekend holiday on Monday august first and
                will reopen on Tuesday August second at 9 a.m. .

                For a faster response please visit our website at man with a wrench dot com and
                to fill out the service request forms.

                Man with the wrench wishes everyone is safe and happy long weekend.

            ', $this->prosodyOptions());
            $response->hangup();
        }

        return $response;
    }
}
