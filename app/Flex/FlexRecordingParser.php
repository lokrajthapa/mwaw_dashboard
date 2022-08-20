<?php

namespace App\Flex;

use App\Models\FlexCall;
use App\Models\FlexConference;
use Carbon\Carbon;
use Twilio\Rest\Api\V2010\Account\RecordingInstance;

class FlexRecordingParser
{
    static public function parse(RecordingInstance $recording)
    {

        $call = FlexCall::query()->whereNotNull('sid')
            ->where('sid', $recording->callSid)->select(['id', 'sid'])->first();

        $conference = FlexConference::query()->whereNotNull('sid')
            ->where('sid', $recording->conferenceSid)->select(['id', 'sid'])->first();

        return [
            'sid' => $recording->sid,
            'conference_sid' => $recording->conferenceSid,
            'conference_id' => $conference?->id,
            'call_sid' => $recording->callSid,
            'call_id' => $call?->id,
            'account_sid' => $recording->accountSid,
            'media_url' => $recording->mediaUrl,
            'json' => $recording->toArray(),
            'duration' => $recording->duration,
            'source' => $recording->source,
            'created_at' => new Carbon($recording->dateCreated),
        ];
    }
}
