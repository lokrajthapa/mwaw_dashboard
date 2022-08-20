<?php

namespace App\Ivr;

use Illuminate\Support\Facades\Log;
use Twilio\TwiML\VoiceResponse;

class NewCallbackNumberHandler extends Base implements StateInterface
{

    public function execute($number): VoiceResponse
    {
        request()->merge(['callbackNumber' => $number]);
        $resp = CallbackCreator::create();
//        Log::debug('create task response', [$resp->status(), $resp->body()]);

        $response = new VoiceResponse();
        $say = $response->say('', $this->sayOptions());
        $say->prosody($this->speech('callbackRegistered'), $this->prosodyOptions());
        $response->hangup();
        return $response;
    }
}
