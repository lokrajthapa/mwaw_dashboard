<?php

namespace App\Http\Controllers;

use App\Http\Resources\JobNumberAlertResource;
use App\Models\JobNumberAlert;
use Illuminate\Http\Request;

class JobNumberAlertController extends Controller
{

    public function __construct()
    {
        $this->middleware(['auth:sanctum']);
        $this->middleware('fix.pagination')->only('index');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Resources\Json\AnonymousResourceCollection
     */
    public function index(Request $request)
    {
        $query = JobNumberAlert::query();

        if ($request->has('with')) {
            $query->with($request->with);
        }

        if ($request->has('filters')) {
            $query = getQueryWithFilters(json_decode($request->filters, true), $query);
        }

        $query->orderBy($request->sortBy ?: 'created_at', $request->sortDesc == 'true' ? 'desc' : 'asc');

        return JobNumberAlertResource::collection($query->paginate($request->itemsPerPage));

    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @return JobNumberAlertResource
     */
    public function store(Request $request)
    {
        $jobNumberAlert = JobNumberAlert::create($request->all());

        return new JobNumberAlertResource($jobNumberAlert);
    }

    /**
     * Display the specified resource.
     *
     * @param \App\Models\JobNumberAlert $jobNumberAlert
     * @return JobNumberAlertResource
     */
    public function show(JobNumberAlert $jobNumberAlert)
    {
        return new JobNumberAlertResource($jobNumberAlert);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param \App\Models\JobNumberAlert $jobNumberAlert
     * @return JobNumberAlertResource
     */
    public function update(Request $request, JobNumberAlert $jobNumberAlert)
    {
        $jobNumberAlert->update($request->all());

        return new JobNumberAlertResource($jobNumberAlert);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param \App\Models\JobNumberAlert $jobNumberAlert
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy(JobNumberAlert $jobNumberAlert)
    {
        $jobNumberAlert->delete();
        return response()->json(['message' => 'success']);
    }
}
