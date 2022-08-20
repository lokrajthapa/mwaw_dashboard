<?php

namespace App\Onesignal;

use Illuminate\Support\Facades\Http;

class Onesignal
{

    static public function send($messgae, $playerIds, $url = null)
    {
        return Http::acceptJson()->asJson()->withHeaders([
            'Authorization' => 'Basic ' . env('ONESIGNAL_KEY')
        ])->post('https://onesignal.com/api/v1/notifications', [
            'include_player_ids' => $playerIds,
            'contents' => [
                'en' => $messgae
            ],
            'url' => env('FRONTEND_URL'),
            'app_id' => env('ONESIGNAL_APPID')
        ]);
    }
}
