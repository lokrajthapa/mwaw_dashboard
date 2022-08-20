<?php

namespace App\Http\Controllers;

use App\Http\Resources\StatusResource;
use App\Models\Status;
use Illuminate\Http\Request;

class StatusController extends Controller
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
        $query = Status::query()
            ->where('name', 'like', '%' . $request->search . '%');

        if ($request->has('with')) {
            $query->with($request->with);
        }

        if ($request->has('filters')) {
            $query = getQueryWithFilters(json_decode($request->filters, true), $query);
        }

        $query->orderBy($request->sortBy ?: 'created_at', $request->sortDesc == 'true' ? 'desc' : 'asc');

        return StatusResource::collection($query->paginate($request->itemsPerPage));

    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @return StatusResource
     */
    public function store(Request $request)
    {
        $status = Status::create($request->all());
        return new StatusResource($status);
    }

    /**
     * Display the specified resource.
     *
     * @param \App\Models\Status $status
     * @return StatusResource
     */
    public function show(Status $status)
    {
        return new StatusResource($status);

    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param \App\Models\Status $status
     * @return StatusResource
     */
    public function update(Request $request, Status $status)
    {
        $status->update($request->all());
        return new StatusResource($status);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param \App\Models\Status $status
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy(Status $status)
    {
        $status->delete();
        return response()->json(['message' => 'success']);
    }
}
