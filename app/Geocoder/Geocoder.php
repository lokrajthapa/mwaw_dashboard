<?php

namespace App\Geocoder;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;

class Geocoder
{

    private $apiKey;

    public function __construct()
    {
        $this->apiKey = env('MAPS_API_KEY');
    }

    public function getCoordinates(string $address)
    {
        $url = 'https://maps.googleapis.com/maps/api/geocode/json';
        $params = [
            'address' => $address,
            'key' => $this->apiKey
        ];
        return Cache::store('redis')->rememberForever('lat_lng-' . $address, function () use ($url, $params, $address) {
            echo 'Get ' . $address . PHP_EOL;
            $response = Http::contentType('application/json')->get($url, $params);
            $data = $response->json()['results'][0];
            return [
                'latitude' => strval($data['geometry']['location']['lat']),
                'longitude' => strval($data['geometry']['location']['lng']),
            ];
        });
    }
}
