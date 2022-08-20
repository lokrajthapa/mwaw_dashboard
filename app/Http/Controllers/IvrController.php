<?php

namespace App\Http\Controllers;

use App\Ivr\AgentConnector;
use App\Ivr\AgentRingtoneHandler;
use App\Ivr\ApplianceHandler;
use App\Ivr\CallbackHandler;
use App\Ivr\CallbackOrVoicemailHandler;
use App\Ivr\CallEnqueueHandler;
use App\Ivr\ConfirmCallbackNumberHandler;
use App\Ivr\Greeting;
use App\Ivr\LeaveQueue;
use App\Ivr\MainSelectionHandler;
use App\Ivr\MainSelector;
use App\Ivr\NewCallbackNumberHandler;
use App\Ivr\ServiceTypeHandler;
use App\Ivr\StateInterface;
use App\Ivr\VoicemailHandler;
use App\Models\FlexCall;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Twilio\TwiML\VoiceResponse;

class IvrController extends Controller
{

    public function handle(Request $request)
    {
//        Log::debug('ivr requests', $request->all());
        $this->updateFlexCall($request);
        $mode = $request->input('mode');
        return response(match ($mode) {
            'mainSelection' => $this->callState(new MainSelector()),

            'handleMainSelection' => $this->callState(new MainSelectionHandler(), $request->Digits ?: $request->mainSelection),

            'handleCallback' => $this->callState(new CallbackHandler()),

            'handleVoicemail' => $this->callState(new VoicemailHandler()),

            'handleConfirmCallbackNumber' => $this->callState(new ConfirmCallbackNumberHandler(), $request->Digits ?: $request->callbackOption),

            'handleNewCallbackNumber' => $this->callState(new NewCallbackNumberHandler(), $request->Digits ?: $request->callbackNumber),

            'connectToAgent' => $this->callState(new AgentConnector()),

            'connectToAgentRingtone' => $this->callState(new AgentRingtoneHandler()),

            'leaveQueue' => $this->callState(new LeaveQueue()),

            'handleCallbackOrVoicemail' => $this->callState(new CallbackOrVoicemailHandler()),

            'handleCallEnqueue' => $this->callState(new CallEnqueueHandler(), $request->Digits),

            'handleServiceType' => $this->callState(new ServiceTypeHandler(), $request->Digits ?: $request->serviceType),

            'handleAppliance' => $this->callState(new ApplianceHandler(), $request->Digits ?: $request->applianceAction),

            default => $this->callState(new Greeting()),

        }, 200, ['Content-Type' => 'text/xml']);
    }


    public function callState(StateInterface $state, ...$params): VoiceResponse
    {
        return $state->execute(...$params);
    }

    public function updateFlexCall(Request $request)
    {
        FlexCall::query()->updateOrCreate(['sid' => $request->input('CallSid')], [
            'sid' => $request->input('CallSid'),
            'status' => $request->input('CallStatus'),
            'direction' => $request->input('Direction'),
            'from' => $request->input('From'),
            'to' => $request->input('To')
        ]);
    }
}
