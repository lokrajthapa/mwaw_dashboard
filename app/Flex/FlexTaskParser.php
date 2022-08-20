<?php

namespace App\Flex;

use Carbon\Carbon;
use Twilio\Rest\Taskrouter\V1\Workspace\TaskInstance;

class FlexTaskParser
{
    static public function parse(TaskInstance $task)
    {
        return [
            'sid' => $task->sid,
            'status' => $task->assignmentStatus,
            'age' => $task->age,
            'attributes' => $task->attributes,
            'queue_name' => $task->taskQueueFriendlyName,
            'channel_name' => $task->taskChannelUniqueName,
            'created_at' => new Carbon($task->dateCreated),
        ];
    }
}
