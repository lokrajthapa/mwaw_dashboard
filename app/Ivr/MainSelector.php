<?php

namespace App\Ivr;

use Twilio\TwiML\VoiceResponse;

class MainSelector extends Base implements StateInterface
{

    public function execute($params = null): VoiceResponse
    {
        $response = new VoiceResponse();

        $gather = $response->gather($this->gatherOptions([
            'action' => $this->action(['mode' => 'handleMainSelection'])
        ]));

        $say = $gather->say('', $this->sayOptions(['loop' => 3]));
        $say->prosody($this->speech('mainSelection'), $this->prosodyOptions());
        $say->break_(['time' => '4s']);

        return $this->withNoInputResponse($response);
    }
}
