<?php

namespace App\Ivr;

use Twilio\TwiML\VoiceResponse;

class ServiceTypeHandler extends Base implements StateInterface
{

    public function execute($option): VoiceResponse
    {
        $response = new VoiceResponse();

        switch ($option) {
            case '1':
                $gather = $response->gather($this->gatherOptions([
                    'action' => $this->action(['mode' => 'handleAppliance', 'serviceType' => $option])
                ]));
                $say = $gather->say('', $this->sayOptions(['loop' => 3]));
                $say->prosody($this->speech('applianceOptions'), $this->prosodyOptions());
                $say->break_(['time' => '4s']);

                return $this->withNoInputResponse($response);

            case '2':
            case '3':
            case '4':
            case '5':
                $gather = $response->gather($this->gatherOptions([
                    'finishOnKey' => '*',
                    'action' => $this->action(['mode' => 'handleCallback', 'serviceType' => $option])
                ]));
                $say = $gather->say('', $this->sayOptions());
                $say->prosody($this->speech('callback'), $this->prosodyOptions());

                $response->redirect($this->action([
                    'mode' => 'connectToAgent', 'serviceType' => $option
                ]));

                return $this->withNoInputResponse($response);

            case '*':
                $response->redirect($this->action(['mode' => 'mainSelection']));
                return $response;

            default:
                return $this->loopForMaxRetry($response, 'handleMainSelection', 'handleServiceType');
        }
    }
}
