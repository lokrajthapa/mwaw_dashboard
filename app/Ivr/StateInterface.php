<?php

namespace App\Ivr;

use Twilio\TwiML\VoiceResponse;

interface StateInterface
{
    public function execute($params): VoiceResponse;
}
