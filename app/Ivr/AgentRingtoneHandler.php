<?php

namespace App\Ivr;

use Twilio\TwiML\VoiceResponse;

class AgentRingtoneHandler extends Base implements StateInterface
{

    public function execute($params=null): VoiceResponse
    {
        $response = new VoiceResponse();
        $gather = $response->gather($this->gatherOptions([
            'action' => $this->action(['mode' => 'leaveQueue'])
        ]));
        $say = $gather->say('', $this->sayOptions(['loop' => 3]));
        $say->prosody($this->speech('callbackOrVoicemail'), $this->prosodyOptions());
        $say->break_(['time' => '3s']);

        return $response;
    }
}
