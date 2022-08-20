<?php

namespace App\Fordpass;

use Carbon\Carbon;
use Exception;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;

class FordpassVehicle
{

    private string $vin;

    public function __construct($vin)
    {
        $this->vin = $vin;
    }

    /**
     * @throws \Illuminate\Http\Client\RequestException
     * @throws \Psr\SimpleCache\InvalidArgumentException
     * @throws \Throwable
     */
    public function status()
    {
        $json = Http::get('http://localhost:' . env('FORDPASS_PORT') . '/status', ['vin' => $this->vin])->json();

        return $json['status']['vehiclestatus'];
    }
}
