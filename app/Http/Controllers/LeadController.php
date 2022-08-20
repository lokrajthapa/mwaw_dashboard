<?php

namespace App\Http\Controllers;

use App\Http\Resources\LeadResource;
use App\Models\Lead;
use Illuminate\Http\Request;

class LeadController extends Controller
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
        $query = Lead::query();
        if ($request->has('with')) {
            $query->with($request->with);
        }

        if ($request->has('filters')) {
            $query = getQueryWithFilters(json_decode($request->filters, true), $query);
        }

        $query->orderBy($request->sortBy ?: 'created_at', $request->sortDesc == 'true' ? 'desc' : 'asc');

        return LeadResource::collection($query->paginate($request->itemsPerPage));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @return LeadResource
     */
    public function store(Request $request)
    {
        $lead = Lead::create($request->all());
        return new LeadResource($lead);
    }

    /**
     * Display the specified resource.
     *
     * @param \App\Models\Lead $lead
     * @return LeadResource
     */
    public function show(Lead $lead)
    {
        return new LeadResource($lead);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param \App\Models\Lead $lead
     * @return LeadResource
     */
    public function update(Request $request, Lead $lead)
    {
        $lead->update($request->all());
        return new LeadResource($lead);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param \App\Models\Lead $lead
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy(Lead $lead)
    {
        $lead->delete();
        return response()->json(['message' => 'success']);
    }
}
