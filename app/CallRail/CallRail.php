<?php

namespace App\CallRail;

use Illuminate\Support\Facades\Http;

class CallRail
{
    public array $config;

    public function __construct()
    {
        $this->config = config('callrail');
    }

    public function params($params = []): array
    {
        $baseParams = [
            'page' => 1,
            'per_page' => 250,
            'sort' => 'start_time',
            'order' => 'desc',
            'fields' => 'gclid'
        ];
        return array_merge($baseParams, $params);
    }

    public function getLatestCalls($page = 1): array
    {
        $data = [];
        for ($i = 1; $i <= $page; $i++) {
            $response = $this->getCallsRequest($this->params(['page' => $i]));
            $data = array_merge($data, $response['calls']);
        }
        return $data;
    }

    public function getAllCalls(): array
    {
        $response = $this->getCallsRequest($this->params(['page' => 1]));

        $page = $response['total_pages'];
        $data = $response['calls'];

        for ($i = 2; $i <= $page; $i++) {
            $response = $this->getCallsRequest($this->params(['page' => $i]));
            $data = array_merge($data, $response['calls']);
        }
        return $data;
    }

    public function getCallsRequest($params = [])
    {
        return Http::withHeaders([
            'Authorization' => 'Token token=' . $this->config['token']
        ])
            ->get('https://api.callrail.com/v3/a/' . $this->config['accountId'] . '/calls.json', $params);
    }
}
