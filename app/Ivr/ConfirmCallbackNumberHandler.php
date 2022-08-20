<?php

namespace App\Ivr;

use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use Twilio\TwiML\VoiceResponse;

class ConfirmCallbackNumberHandler extends Base implements StateInterface
{

    public function execute($option): VoiceResponse
    {
        $response = new VoiceResponse();

        if ($option == '1') {
            $resp = CallbackCreator::create();
//            Log::debug('create task response', [$resp->status(), $resp->body()]);
            $say = $response->say('', $this->sayOptions());
            $say->prosody($this->speech('callbackRegistered'), $this->prosodyOptions());
            $response->hangup();
            return $response;

        } elseif ($option == '2') {
            $gather = $response->gather($this->gatherOptions([
                'numDigits' => 10,
                'action' => $this->action(['mode' => 'handleNewCallbackNumber', 'confirmCallbackOption' => $option])
            ]));
            $say = $gather->say('', $this->sayOptions(['loop' => 3]));
            $say->prosody($this->speech('newCallbackNumber'), $this->prosodyOptions());
            $say->break_(['time' => '4s']);

            return $this->withNoInputResponse($response);

        } else {
            return $this->loopForMaxRetry($response, 'handleCallback', 'handleConfirmCallbackNumber');
        }
    }
}
