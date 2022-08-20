<?php

namespace App\Ivr;

use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class CallbackCreator
{
    public static function create(): Response
    {
//        Log::debug('create task params', request()->all());
        $url = 'https://plugin-queued-callbacks-voicemail-functions-5231-dev.twil.io/create-callback-task';
        return Http::get($url, request()->all());
    }
}
