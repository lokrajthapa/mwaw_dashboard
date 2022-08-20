<?php

use Illuminate\Contracts\Database\Query\Builder;
use Twilio\Rest\Api\V2010\Account\MessageInstance;
use Twilio\Rest\Client as TwilioClient;

function getQueryWithFilters($filters, Builder $query): Builder
{
    foreach ($filters as $filter => $value) {
        if (is_array($value) && $value) {
            $query->whereIn($filter, $value);
        } else {
            if ($value) {
                $query->where($filter, $value);
            }
        }
    }
    return $query;
}

function sendSms($from, $to, $message): MessageInstance
{
    $account_sid = env('TWILIO_ACCOUNT_SID');
    $auth_token = env('TWILIO_AUTH_TOKEN');
    $client = new TwilioClient($account_sid, $auth_token);
    return $client->messages->create($to, [
        'from' => $from,
        'body' => $message
    ]);
}
