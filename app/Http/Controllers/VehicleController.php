<?php

namespace App\Http\Controllers;

use App\Http\Resources\VehicleResource;
use App\Models\Vehicle;
use Illuminate\Http\Request;

class VehicleController extends Controller
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
        $query = Vehicle::query()
            ->where('id', 'like', '%' . $request->search . '%');

        if ($request->has('with')) {
            $with = $request->input('with');
            if (in_array('latestStatus', $with)) {
                unset($with['latestStatus']);
            }
            $query->with($request->with);
        }

        if ($request->has('filters')) {
            $query = getQueryWithFilters(json_decode($request->filters, true), $query);
        }

        $query->orderBy($request->sortBy ?: 'created_at', $request->sortDesc == 'true' ? 'desc' : 'asc');

        $resource = $query->paginate($request->itemsPerPage);
        if ($request->has('with') && in_array('latestStatus', $request->input('with'))) {
            foreach ($resource->items() as $item) {
                $item->load('latestStatus');
            }
        }
        return VehicleResource::collection($resource);

    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @return VehicleResource
     */
    public function store(Request $request)
    {
        $vehicle = Vehicle::create($request->all());
        return new VehicleResource($vehicle);
    }

    /**
     * Display the specified resource.
     *
     * @param \App\Models\Vehicle $vehicle
     * @return VehicleResource
     */
    public function show(Vehicle $vehicle)
    {
        return new VehicleResource($vehicle);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param \App\Models\Vehicle $vehicle
     * @return VehicleResource
     */
    public function update(Request $request, Vehicle $vehicle)
    {
        $vehicle->update($request->all());
        return new VehicleResource($vehicle);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param \App\Models\Vehicle $vehicle
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy(Vehicle $vehicle)
    {
        $vehicle->delete();
        return response()->json(['message' => 'success']);
    }

    public function latestStatus()
    {
        $vehicles = Vehicle::all();
        foreach ($vehicles as $vehicle) {
            $vehicle->load('latestStatus');
        }
        return VehicleResource::collection($vehicles);
    }
}
