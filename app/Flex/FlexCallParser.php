<?php

namespace App\Flex;

use Carbon\Carbon;
use Twilio\Rest\Api\V2010\Account\CallInstance;

class FlexCallParser
{
    static public function parse(CallInstance $call)
    {
        return [
            'sid' => $call->sid,
            'status' => $call->status,
            'direction' => $call->direction,
            'from' => $call->from,
            'to' => $call->to,
            'duration' => $call->duration,
            'created_at' => new Carbon($call->dateCreated),
        ];
    }
}
