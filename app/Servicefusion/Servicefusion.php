<?php

namespace App\Servicefusion;

use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;

class Servicefusion
{

    private $config;

    public function __construct()
    {
        $this->config = config('servicefusion');
    }

    public function getAccessToken()
    {
        echo 'Get access token' . PHP_EOL;
        $url = $this->config['baseUrl'] . '/oauth/access_token';
        $data = [
            'grant_type' => 'client_credentials',
            'client_id' => $this->config['clientId'],
            'client_secret' => $this->config['clientSecret']
        ];
        $response = Http::contentType('application/json')->post($url, $data);
        $response->throw();
        $data = $response->json();
        return [
            'access_token' => $data['access_token'],
            'token_type' => $data['token_type'],
            'expires_in' => $data['expires_in'],
            'refresh_token' => $data['refresh_token'],
            'created_at' => Carbon::now()->toDateTimeString(),
        ];
    }

    public function refreshAccessToken($refreshToken)
    {
        echo 'Get refresh token' . PHP_EOL;
        $url = $this->config['baseUrl'] . '/oauth/access_token';
        $data = [
            'grant_type' => 'refresh_token',
            'refresh_token' => $refreshToken
        ];
        $response = Http::contentType('application/json')->post($url, $data);
        $response->throw();
        $data = $response->json();
        return [
            'access_token' => $data['access_token'],
            'token_type' => $data['token_type'],
            'expires_in' => $data['expires_in'],
            'refresh_token' => $data['refresh_token'],
            'created_at' => Carbon::now()->toDateTimeString(),
        ];
    }

    public function checkTokenExpired($expiresIn, $createdAt): bool
    {
        $createdAtDate = new Carbon($createdAt);
        return Carbon::now()->greaterThanOrEqualTo($createdAtDate->addSeconds($expiresIn - 60));
    }

    public function getToken()
    {
        $token = Cache::store('redis')->rememberForever('servicefusion-token', function () {
            return $this->getAccessToken();
        });
        //check if expired
        $expired = $this->checkTokenExpired(intval($token['expires_in']), $token['created_at']);
        if ($expired) {
            $token = $this->refreshAccessToken($token['refresh_token']);
            Cache::store('redis')->set('servicefusion-token', $token);
        }
        return $token;
    }

    /**
     * @throws \Illuminate\Http\Client\RequestException
     */
    public function sendRequest($url, $params = [], $method = 'GET')
    {
        $token = $this->getToken();
        $fullUrl = $this->config['baseUrl'] . '/' . $this->config['version'] . $url;
        $headers = [
            'Authorization' => 'Bearer ' . $token['access_token'],
        ];
        if ($method == 'GET') {
            $resp = Http::withHeaders($headers)->timeout(10)->connectTimeout(10)->accept('application/json')->get($fullUrl . '?', $params);
        } else {
            $resp = Http::withHeaders($headers)->timeout(10)->connectTimeout(10)->contentType('application/json')->accept('application/json')->post($fullUrl, $params);
        }
        return $resp;
    }

    /**
     * @throws \Illuminate\Http\Client\RequestException
     */
    public function getRequest($route, $params = [], $pageCount = null)
    {
        $responseItems = collect([]);
        $defaultParams = [
            'per-page' => 50,
            'page' => 1,
        ];
        $mergedParams = array_merge($defaultParams, $params);
        $response = $this->sendRequest($route, $mergedParams)->json();
        $responseItems = $responseItems->concat($response['items']);
        $meta = $response['_meta'];
        $i = 2;
        while ($i <= ($pageCount ?: $meta['pageCount'])) {
            echo 'Page ' . $i . ' of ' . $meta['pageCount'] . PHP_EOL;
            $mergedParams['page'] = $i;
            try {
                $response = $this->sendRequest($route, $mergedParams);
                echo 'X-Rate-Limit-Remaining ' . $response->header('X-Rate-Limit-Remaining') . PHP_EOL;
                $response = $response->json();
                $meta = $response['_meta'];
                $responseItems = $responseItems->concat($response['items']);
                $i++;
                sleep(2);
            } catch (\Exception $e) {
                error_log($e->getMessage());
                echo 'sleeping for 60 sec' . PHP_EOL;
                sleep(60);
            }
        }
        return $responseItems;
    }

    public function getAllStatuses(): Collection
    {
        $route = '/job-statuses';
        return $this->getRequest($route);
    }

    public function getAllCategories(): Collection
    {
        $route = '/job-categories';
        return $this->getRequest($route);
    }

    public function getAllCustomers(): Collection
    {
        $route = '/customers';
        $params = [
            'expand' => 'contacts,contacts.phones,contacts.emails,locations',
        ];
        return $this->getRequest($route, $params);
    }

    public function getAllTechnicians(): Collection
    {
        $route = '/techs';
        return $this->getRequest($route);
    }

    public function getLatestCustomers($pageCount = 10): Collection
    {
        $route = '/customers';
        $params = [
            'expand' => 'contacts,contacts.phones,contacts.emails,locations',
            'sort' => '-created_at'
        ];
        return $this->getRequest($route, $params, $pageCount);
    }

    public function getLatestJobs($pageCount = 10): Collection
    {
        $route = '/jobs';
        $params = [
            'expand' => 'techs_assigned,custom_fields,products,services,payments',
            'sort' => '-created_at'
        ];
        return $this->getRequest($route, $params, $pageCount);
    }

    public function getAllJobs(): Collection
    {
        $route = '/jobs';
        $params = [
            'expand' => 'techs_assigned,custom_fields,products,services,payments',
        ];
        return $this->getRequest($route, $params);
    }

    public function getJobById($id)
    {
        $params = [
            'expand' => 'techs_assigned,custom_fields,products,services,payments',
        ];
        return $this->sendRequest('/jobs/' . $id, $params)->json();
    }
}
