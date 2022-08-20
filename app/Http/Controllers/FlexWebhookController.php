<?php

namespace App\Http\Controllers;

use App\Flex\FlexCallParser;
use App\Flex\FlexRecordingParser;
use App\Models\FlexCall;
use App\Models\FlexConference;
use App\Models\FlexRecording;
use App\Models\FlexTask;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Twilio\Rest\Client;

class FlexWebhookController extends Controller
{
    public Client $client;

    public function __construct()
    {
        $this->client = new Client(env('TWILIO_FLEX_ACCOUNT_SID'), env('TWILIO_FLEX_AUTH_TOKEN'));
    }

    public function handle(Request $request)
    {
        $eventType = $request->input('EventType');

        if (str_contains($eventType, 'reservation')) {
            $this->handleReservationEvent($request);
        } elseif (str_contains($eventType, 'task')) {
            $this->handleTaskEvent($request);
        }

        return response()->json(['message' => 'success']);
    }

    public function handleConference(Request $request)
    {
//        Log::debug('handle conference', $request->all());
        $flexConference = FlexConference::query()->where('sid', $request->ConferenceSid)->first();

        if (!$flexConference) {
            $conference = $this->client->conferences($request->ConferenceSid)->fetch();
            $flexConference = FlexConference::query()->updateOrCreate(['sid' => $conference->sid], [
                'sid' => $conference->sid,
                'created_at' => new Carbon($conference->dateCreated)
            ]);
        }

        $event = $request->input('StatusCallbackEvent');
        switch ($event) {
            case 'participant-join':
            case 'participant-leave':
                $call = $this->client->calls($request->CallSid)->fetch();
                $params = FlexCallParser::parse($call);
                $params['conference_id'] = $flexConference->id;

                FlexCall::query()->updateOrCreate(['sid' => $call->sid], $params);

                if (str_contains($call->to, '+') && str_contains($call->from, '+')) {
                    $flexConference->update(['direction' => $call->direction]);
                }

                break;
            case 'conference-start':
            case 'conference-end':
                $recordings = $this->client->recordings->read(['conferenceSid' => $flexConference->sid], 1);
                if ($recordings) {
                    FlexRecording::query()->updateOrCreate(['sid' => $recordings[0]->sid], FLexRecordingParser::parse($recordings[0]));
                }
                break;
            default:
                break;
        }
        return response()->json(['message' => 'success']);
    }

    public function handleRecording(Request $request)
    {
        if ($request->RecordingStatus == 'completed') {
            $recording = $this->client->recordings($request->RecordingSid)->fetch();
            FlexRecording::query()->updateOrCreate(['sid' => $recording->sid], FLexRecordingParser::parse($recording));
        }
        return response()->json(['message' => 'success']);
    }

    public function handleReservationEvent(Request $request)
    {
//        Log::debug('reservation webhook', $request->all());
        $eventType = $request->input('EventType');

        $task = FlexTask::query()->firstOrCreate(['sid' => $request->TaskSid], [
            'sid' => $request->TaskSid,
            'age' => intval($request->TaskAge),
            'status' => $request->TaskAssignmentStatus,
            'attributes' => json_decode($request->TaskAttributes, true),
            'queue_name' => $request->TaskQueueName,
            'channel_name' => $request->TaskChannelUniqueName,
            'created_at' => new Carbon(intval($request->TaskDateCreated))
        ]);

        $workerContactUri = json_decode($request->WorkerAttributes, true)['contact_uri'];
        $workerId = User::query()->where('agent_id', $workerContactUri)->select(['id'])->first()->id;

        $attachedWorkers = DB::table('task_worker')
            ->where('task_id', $task->id)
            ->select(['task_id', 'worker_id'])
            ->get()->pluck('worker_id')->toArray();

        $newWorker = array_diff([$workerId], $attachedWorkers);

        $task->update([
            'age' => intval($request->TaskAge),
            'status' => $request->TaskAssignmentStatus
        ]);

        switch ($eventType) {
            case 'reservation.created':
                if ($newWorker) {
                    $task->workers()->attach([$workerId]);
                }
                break;
            case 'reservation.accepted':
            case 'reservation.completed':
                $task->workers()->sync([$workerId]);
                break;
            case 'reservation.canceled':
            case 'reservation.rejected':
            case 'reservation.rescinded':
                $task->workers()->detach([$workerId]);
                break;
            default:
                break;
        }
    }

    public function handleTaskEvent(Request $request)
    {
        $eventType = $request->input('EventType');

//        Log::debug('task webhook', $request->all());
        $params = [
            'sid' => $request->TaskSid,
            'age' => intval($request->TaskAge),
            'status' => $request->TaskAssignmentStatus,
            'attributes' => json_decode($request->TaskAttributes, true),
            'queue_name' => $request->TaskQueueName,
            'channel_name' => $request->TaskChannelUniqueName,
            'created_at' => new Carbon(intval($request->TaskDateCreated))
        ];

        switch ($eventType) {
            case 'task.created':
                FlexTask::query()->create($params);
                break;
            case 'task.completed':
            case 'task.canceled':
                FlexTask::query()->where('sid', $request->TaskSid)->update($params);
                break;
            case 'task.deleted':
//                Log::debug($eventType, $request->all());
                break;
            default:
                break;
        }
    }
}
