<?php

namespace App\Ivr;

use Twilio\TwiML\VoiceResponse;

class CallbackHandler extends Base implements StateInterface
{

    /**
     * @throws \Twilio\Exceptions\ConfigurationException
     * @throws \Twilio\Exceptions\TwilioException
     */
    public function execute($params = null): VoiceResponse
    {
        $this->removeTask();

        $response = new VoiceResponse();
        $gather = $response->gather($this->gatherOptions([
            'action' => $this->action(['mode' => 'handleConfirmCallbackNumber'])
        ]));
        $say = $gather->say('', $this->sayOptions(['loop' => 3]));
        $say->prosody($this->speech('confirmCallbackNumber', [
            '$number' => implode(' ', str_split(request()->input('From')))
        ]), $this->prosodyOptions());
        $say->break_(['time' => '4s']);

        return $this->withNoInputResponse($response);
    }
}
