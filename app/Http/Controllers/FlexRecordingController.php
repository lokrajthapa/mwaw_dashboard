<?php

namespace App\Http\Controllers;

use App\Http\Resources\FlexRecordingResource;
use App\Models\FlexRecording;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class FlexRecordingController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth:sanctum', 'hasAnyRole:admin'])->except(['webhook']);
        $this->middleware('fix.pagination')->only('index');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Resources\Json\AnonymousResourceCollection
     */
    public function index(Request $request)
    {
        $query = FlexRecording::query()
            ->where(function ($q) use ($request) {
                $q->where('sid', 'like', '%' . $request->search . '%')
                    ->orWhere('conference_sid', 'like', '%' . $request->search . '%');
            });

        if ($request->has('with')) {
            $query->with($request->with);
        }

        if ($request->has('filters')) {
            $query = getQueryWithFilters(json_decode($request->filters, true), $query);
        }

        $query->orderBy($request->sortBy ?: 'created_at', $request->sortDesc == 'true' ? 'desc' : 'asc');

        return FlexRecordingResource::collection($query->paginate($request->itemsPerPage));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @return FlexRecordingResource
     */
    public function store(Request $request)
    {
        $flexRecording = FlexRecording::create($request->all());
        return new FlexRecordingResource($flexRecording);
    }

    /**
     * Display the specified resource.
     *
     * @param \App\Models\FlexRecording $flexRecording
     * @return FlexRecordingResource
     */
    public function show(FlexRecording $flexRecording)
    {
        return new FlexRecordingResource($flexRecording);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param \App\Models\FlexRecording $flexRecording
     * @return FlexRecordingResource
     */
    public function update(Request $request, FlexRecording $flexRecording)
    {
        $flexRecording->update($request->all());
        return new FlexRecordingResource($flexRecording);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param \App\Models\FlexRecording $flexRecording
     * @return JsonResponse
     */
    public function destroy(FlexRecording $flexRecording)
    {
        $flexRecording->delete();
        return response()->json(['message' => 'success']);
    }
}
