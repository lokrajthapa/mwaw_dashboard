<?php

namespace App\Http\Controllers;

use App\Flex\FlexTaskParser;
use App\Http\Resources\FlexTaskResource;
use App\Models\FlexTask;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Twilio\Rest\Client;

class FlexTaskController extends Controller
{

    public function __construct()
    {
        $this->middleware(['auth:sanctum', 'hasAnyRole:admin']);
        $this->middleware('fix.pagination')->only('index');

    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Resources\Json\AnonymousResourceCollection
     */
    public function index(Request $request)
    {
        if ($request->has('page') && $request->page == 1) {
            if (!Cache::store('redis')->has('throttle-task-sync')) {
                try {
                    $client = new Client(env('TWILIO_FLEX_ACCOUNT_SID'), env('TWILIO_FLEX_AUTH_TOKEN'));
                    $tasks = $client->taskrouter->workspaces(env('TWILIO_FLEX_WORKSPACE_SID'))->tasks->read([], 50);
                    foreach ($tasks as $task) {
                        FlexTask::query()->updateOrCreate(['sid' => $task->sid], FlexTaskParser::parse($task));
                    }
                } catch (\Exception $e) {
                }
                Cache::store('redis')->set('throttle-task-sync', true, 10);
            }
        }

        $query = FlexTask::query()
            ->where(function ($q) use ($request) {
                $q->where('attributes', 'like', '%' . $request->search . '%');
            });

        if ($request->has('with')) {
            $query->with($request->with);
        }

        if ($request->has('filters')) {
            $filters = json_decode($request->filters, true);

            if (key_exists('worker_id', $filters)) {
                if ($filters['worker_id']) {
                    $query->whereHas('workers', function ($q) use ($filters) {
                        $q->whereIn('worker_id', $filters['worker_id']);
                    });
                }

                unset($filters['worker_id']);
            }

            $query = getQueryWithFilters($filters, $query);
        }

        $query->orderBy($request->sortBy ?: 'created_at', $request->sortDesc == 'true' ? 'desc' : 'asc');

        return FlexTaskResource::collection($query->paginate($request->itemsPerPage));

    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @return FlexTaskResource
     */
    public function store(Request $request)
    {
        $flexTask = FlexTask::create($request->all());
        return new FlexTaskResource($flexTask);
    }

    /**
     * Display the specified resource.
     *
     * @param \App\Models\FlexTask $flexTask
     * @return FlexTaskResource
     */
    public function show(FlexTask $flexTask)
    {
        return new FlexTaskResource($flexTask);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param \App\Models\FlexTask $flexTask
     * @return FlexTaskResource
     */
    public function update(Request $request, FlexTask $flexTask)
    {
        $flexTask->update($request->all());
        return new FlexTaskResource($flexTask);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param \App\Models\FlexTask $flexTask
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy(FlexTask $flexTask)
    {
        $flexTask->delete();
        return response()->json(['message' => 'success']);
    }
}
