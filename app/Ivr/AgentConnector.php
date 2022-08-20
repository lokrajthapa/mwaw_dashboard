<?php

namespace App\Ivr;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Twilio\TwiML\VoiceResponse;

class AgentConnector extends Base implements StateInterface
{

    public function execute($params = null): VoiceResponse
    {
        $response = new VoiceResponse();
//        Log::debug('agent connector', request()->all());

        $queries = [
            'mainSelection' => request()->input('mainSelection') ?: '',
            'serviceType' => request()->input('serviceType') ?: '',
            'applianceAction' => request()->input('applianceAction') ?: '',
        ];

        $titles = $this->parseSelections();

        request()->merge($queries);

        $enqueue = $response->enqueue(null, [
            'workflowSid' => 'WW58528895a1abedc143b04ba4346851d7',
            'waitUrl' => $this->action(['mode' => 'connectToAgentRingtone'])
        ]);

        $customer = [];
        $data = Http::get('https://flex-api.manwithawrench.com/api/getCustomer',
            ['number' => request()->input('From')])->json();
        if (key_exists('message', $data) && $data['message'] == 'success') {
            $customer = $data['data'];
            $queries['newCustomer'] = false;
        } else {
            $queries['newCustomer'] = true;
        }

        $enqueue->task(json_encode([...$titles, ...$queries, ...$customer]));

        $response->redirect($this->action([
            'mode' => 'handleCallbackOrVoicemail'
        ]));

        return $response;
    }
}
