<?php

namespace App\Ivr;

use Twilio\TwiML\VoiceResponse;

class VoicemailHandler extends Base implements StateInterface
{

    public function execute($params = null): VoiceResponse
    {
        $response = new VoiceResponse();

        $url = 'https://plugin-queued-callbacks-voicemail-functions-5231-dev.twil.io/voicemail-task';

        $query = http_build_query(request()->all());
        $response->redirect($url . '?' . $query);

        return $response;
    }
}
