<?php

namespace App\Ivr;

use Twilio\TwiML\VoiceResponse;

class CallbackOrVoicemailHandler extends Base implements StateInterface
{

    public function execute($params = null): VoiceResponse
    {
        $response = new VoiceResponse();
        $gather = $response->gather($this->gatherOptions([
            'finishOnKey' => '*',
            'action' => $this->action(['mode' => 'handleCallEnqueue'])
        ]));
        $say = $gather->say('', $this->sayOptions(['loop' => 3]));
        $say->prosody($this->speech('callbackOrVoicemail2'), $this->prosodyOptions());
        $say->break_(['time' => '4s']);

        return $this->withNoInputResponse($response);
    }
}
