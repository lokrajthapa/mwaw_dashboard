<?php

namespace App\Ivr;

use Twilio\TwiML\VoiceResponse;

class ApplianceHandler extends Base implements StateInterface
{

    public function execute($option): VoiceResponse
    {
        $response = new VoiceResponse();

        switch ($option) {
            case '1':
            case '2':
                $gather = $response->gather($this->gatherOptions([
                    'finishOnKey' => '*',
                    'action' => $this->action(['mode' => 'handleCallback', 'applianceAction' => $option])
                ]));
                $say = $gather->say('', $this->sayOptions());
                $say->prosody($this->speech('callback'), $this->prosodyOptions());

                $response->redirect($this->action([
                    'mode' => 'connectToAgent', 'applianceAction' => $option
                ]));

                return $this->withNoInputResponse($response);

            case '*':
                $response->redirect($this->action(['mode' => 'handleMainSelection']));
                return $response;

            default:
                return $this->loopForMaxRetry($response, 'handleServiceType', 'handleAppliance');
        }
    }
}
