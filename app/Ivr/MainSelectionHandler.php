<?php

namespace App\Ivr;

use Twilio\TwiML\VoiceResponse;

class MainSelectionHandler extends Base implements StateInterface
{

    public function execute($option): VoiceResponse
    {
        $response = new VoiceResponse();

        switch ($option) {
            case '1':
                $gather = $response->gather($this->gatherOptions([
                    'finishOnKey' => '*',
                    'action' => $this->action(['mode' => 'handleCallback', 'mainSelection' => $option])
                ]));
                $say = $gather->say('', $this->sayOptions());
                $say->prosody($this->speech('callback'), $this->prosodyOptions());
                $response->redirect($this->action(['mode' => 'connectToAgent', 'mainSelection' => $option]));
                return $response;

            case '2':
                $gather = $response->gather($this->gatherOptions([
                    'action' => $this->action(['mode' => 'handleServiceType', 'mainSelection' => $option])
                ]));
                $say = $gather->say('', $this->sayOptions(['loop' => 3]));
                $say->prosody($this->speech('selectServiceType'), $this->prosodyOptions());
                $say->break_(['time' => '4s']);

                return $this->withNoInputResponse($response);

            case '3':
                $gather = $response->gather($this->gatherOptions([
                    'finishOnKey' => '*',
                    'timeout' => 3,
                    'action' => $this->action(['mode' => 'handleCallback', 'mainSelection' => $option])
                ]));
                $say = $gather->say('', $this->sayOptions());
                $say->prosody($this->speech('customerServiceCallback'), $this->prosodyOptions());
                $response->redirect($this->action(['mode' => 'connectToAgent', 'mainSelection' => $option]));
                return $response;

            case '4':
                $gather = $response->gather($this->gatherOptions([
                    'finishOnKey' => '*',
                    'timeout' => 3,
                    'action' => $this->action(['mode' => 'handleCallback', 'mainSelection' => $option])
                ]));
                $say = $gather->say('', $this->sayOptions());
                $say->prosody($this->speech('generalInquiryCallback'), $this->prosodyOptions());
                $response->redirect($this->action(['mode' => 'connectToAgent', 'mainSelection' => $option]));
                return $response;

            default:
                return $this->loopForMaxRetry($response, 'mainSelection', 'handleMainSelection');
        }
    }
}
