<?php

namespace App\Ivr;

use Twilio\TwiML\VoiceResponse;

class LeaveQueue extends Base implements StateInterface
{

    public function execute($params=null): VoiceResponse
    {
        $response = new VoiceResponse();
        $response->leave();
        return $response;
    }
}
