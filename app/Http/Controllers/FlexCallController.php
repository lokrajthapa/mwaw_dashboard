<?php

namespace App\Http\Controllers;

use App\Flex\FlexCallParser;
use App\Flex\FlexRecordingParser;
use App\Http\Resources\FlexCallResource;
use App\Models\FlexCall;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Twilio\Rest\Client;

class FlexCallController extends Controller
{


    /**
     * @throws \Twilio\Exceptions\ConfigurationException
     */
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

        $query = FlexCall::query()
            ->where(function ($q) use ($request) {
                $q->where('from', 'like', '%' . $request->search . '%')
                    ->orWhere('to', 'like', '%' . $request->search . '%');
            });

        if ($request->has('with')) {
            $query->with($request->with);
        }

        if ($request->has('filters')) {
            $filters = json_decode($request->filters, true);

            if (key_exists('from', $filters) && $filters['from']) {
                $query->where(function ($q) use ($filters) {
                    $q->whereIn('from', $filters['from'])
                        ->orWhereIn('to', $filters['from']);
                });
                unset($filters['from']);
            }

            if (key_exists('to', $filters) && $filters['to']) {
                $query->where(function ($q) use ($filters) {
                    $q->whereIn('to', $filters['to'])
                        ->orWhereIn('from', $filters['to']);
                });
                unset($filters['to']);
            }

            $query = getQueryWithFilters($filters, $query);
        }

        $query->orderBy($request->sortBy ?: 'created_at', $request->sortDesc == 'true' ? 'desc' : 'asc');

        return FlexCallResource::collection($query->paginate($request->itemsPerPage));

    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @return FlexCallResource
     */
    public function store(Request $request)
    {
        $flexCall = FlexCall::create($request->all());
        return new FlexCallResource($flexCall);
    }

    /**
     * Display the specified resource.
     *
     * @param \App\Models\FlexCall $flexCall
     * @return FlexCallResource
     */
    public function show(FlexCall $flexCall)
    {
        return new FlexCallResource($flexCall);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param \App\Models\FlexCall $flexCall
     * @return FlexCallResource
     */
    public function update(Request $request, FlexCall $flexCall)
    {
        $flexCall->update($request->all());
        return new FlexCallResource($flexCall);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param \App\Models\FlexCall $flexCall
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy(FlexCall $flexCall)
    {
        $flexCall->delete();
        return response()->json(['message' => 'success']);
    }
}
