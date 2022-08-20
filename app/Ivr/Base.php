<?php

namespace App\Ivr;

use Twilio\Rest\Client;
use Twilio\TwiML\VoiceResponse;

class Base
{
    public array $config;

    public function __construct()
    {
        $this->config = config('ivr');
    }

    public function withNoInputResponse(VoiceResponse $response): VoiceResponse
    {
        $say = $response->say('', $this->sayOptions());
        $say->prosody($this->speech('noInput'), $this->prosodyOptions());
        $response->hangup();
        return $response;
    }

    public function invalidInput(VoiceResponse $response): VoiceResponse
    {
        $response->say('Invalid entry', $this->sayOptions());
        $response->hangup();
        return $response;
    }

    public function loopForMaxRetry(VoiceResponse $response, $mode, $retryMode): VoiceResponse
    {
        $retry = request()->input('retry');
        $say = $response->say('', $this->sayOptions());
        if (!$retry) {
            $say->prosody($this->speech('invalidInput'), $this->prosodyOptions());

            $response->redirect($this->action(['mode' => $mode, 'retry' => 1, 'retryMode' => $retryMode]));
        } elseif ($retry < 3) {
            $say->prosody($this->speech('invalidInput'), $this->prosodyOptions());

            $response->redirect($this->action(['mode' => $mode, 'retry' => 1 + $retry, 'retryMode' => $retryMode]));
        } else {
            $say->prosody($this->speech('exceededRetry'), $this->prosodyOptions());
            $response->hangup();
        }
        return $response;
    }

    public function gatherOptions($options = []): array
    {
        $baseOptions = [
            'input' => 'dtmf',
            'timeout' => 5,
            'numDigits' => 1,
            'finishOnKey' => '#',
            'method' => 'POST',
        ];
        return array_merge($baseOptions, $options);
    }

    public function sayOptions($options = []): array
    {
        $baseOptions = [
            'voice' => 'Polly.Matthew-Neural',
            'loop' => 1
        ];
        return array_merge($baseOptions, $options);
    }

    public function prosodyOptions($options = []): array
    {
        $baseOptions = [
            'rate' => '90%'
        ];
        return array_merge($baseOptions, $options);
    }

    /**
     * @throws \Twilio\Exceptions\ConfigurationException
     * @throws \Twilio\Exceptions\TwilioException
     */
    public function removeTask(): void
    {
        $client = new Client(env('TWILIO_FLEX_ACCOUNT_SID'), env('TWILIO_FLEX_AUTH_TOKEN'));
        $taskSid = request()->input('taskSid');
        if ($taskSid) {
            $client->taskrouter->workspaces(env('TWILIO_FLEX_WORKSPACE_SID'))->tasks($taskSid)->delete();
        }
    }

    public function action($params = []): string
    {
        $requests = request()->all();

        unset($requests['Digits']);
        unset($requests['CallToken']);
        unset($requests['StirPassportToken']);

        if (request()->has('retryMode')) {
            if ($requests['retryMode'] != $requests['mode']) {
                unset($requests['retryMode']);
            }
        }

        $mergedParams = array_merge($requests, $params);
        return route('ivr', $mergedParams);
    }

    public function speech($key, $replaceStrings = []): string
    {
        return strtr($this->config['lang'][$key], $replaceStrings);
    }

    public function parseSelections(): array
    {
        $mainSelection = request()->input('mainSelection');
        $serviceType = request()->input('serviceType');
        $applianceAction = request()->input('applianceAction');
        $mainSelectionOptions = [
            '1' => 'Schedule a new repair over the phone',
            '2' => 'Update on an existing repair, such as parts update or a quote',
            '3' => 'Follow up on a customer service inquiry',
            '4' => 'Any other general inquiries or questions',
        ];
        $serviceTypeOptions = [
            '1' => 'Appliance Repair',
            '2' => 'Plumbing',
            '3' => 'Electrical',
            '4' => 'HVAC',
            '5' => 'More than one service',
        ];
        $applianceActionOptions = [
            '1' => 'Parts',
            '2' => 'Quote update',
        ];
        $mainSelectionTitle = $mainSelection ? $mainSelectionOptions[$mainSelection] : '';
        $serviceTypeTitle = $serviceType ? $serviceTypeOptions[$serviceType] : '';
        $applianceActionTitle = $applianceAction ? $applianceActionOptions[$applianceAction] : '';
        return [
            'mainSelectionTitle' => $mainSelectionTitle,
            'serviceTypeTitle' => $serviceTypeTitle,
            'applianceActionTitle' => $applianceActionTitle
        ];
    }
}
