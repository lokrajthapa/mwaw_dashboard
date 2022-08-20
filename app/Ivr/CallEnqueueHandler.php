<?php

namespace App\Ivr;

use Twilio\TwiML\VoiceResponse;

class CallEnqueueHandler extends Base implements StateInterface
{

    public function execute($params): VoiceResponse
    {
        $response = new VoiceResponse();
        switch ($params) {
            case '9':
                $response->redirect($this->action(['mode' => 'handleVoicemail']));
                return $response;
            case '#':
                $response->redirect($this->action(['mode' => 'handleCallback']));
                return $response;
            default:
                return $this->loopForMaxRetry($response, 'handleCallbackOrVoicemail', 'handleCallEnqueue');
        }
    }
}
