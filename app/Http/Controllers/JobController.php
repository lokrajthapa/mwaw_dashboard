<?php

namespace App\Http\Controllers;

use App\Http\Resources\JobResource;
use App\Models\Job;
use Illuminate\Http\Request;

class JobController extends Controller
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
        $query = Job::query()
            ->where(function ($q) use ($request) {
                $q->where('title', 'like', '%' . $request->search . '%')
                    ->orWhere('po', 'like', '%' . $request->search . '%')
                    ->orWhereHas('customer', function ($qu) use ($request) {
                        $qu->where('first_name', 'like', '%' . $request->search . '%')
                            ->orWhere('last_name', 'like', '%' . $request->search . '%');
                    });
            });

        if ($request->has('with')) {
            $query->with($request->with);
        }

        if ($request->has('filters')) {
            $query = getQueryWithFilters(json_decode($request->filters, true), $query);
        }

        $query->orderBy($request->sortBy ?: 'created_at', $request->sortDesc == 'true' ? 'desc' : 'asc');

        return JobResource::collection($query->paginate($request->itemsPerPage));

    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @return JobResource
     */
    public function store(Request $request)
    {
        $job = Job::create($request->all());

        return $this->syncTechnicians($request, $job);
    }

    /**
     * Display the specified resource.
     *
     * @param \App\Models\Job $job
     * @return JobResource
     */
    public function show(Job $job)
    {
        $job->load(['status', 'technicians']);
        return new JobResource($job);

    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param \App\Models\Job $job
     * @return JobResource
     */
    public function update(Request $request, Job $job)
    {
        $job->update($request->all());
        return $this->syncTechnicians($request, $job);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param \App\Models\Job $job
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy(Job $job)
    {
        $job->delete();
        return response()->json(['message' => 'success']);
    }

    /**
     * @param Request $request
     * @param $job
     * @return JobResource
     */
    public function syncTechnicians(Request $request, $job): JobResource
    {
        if ($request->has('technicians')) {
            $syncItems = [];
            foreach ($request->technicians as $technician) {
                if (is_numeric($technician)) {
                    $syncItems[] = $technician;
                } else {
                    $syncItems[] = $technician['id'];
                }
            }
            $job->technicians()->sync($syncItems);
        }
        $job->load(['status', 'technicians']);
        return new JobResource($job);
    }

    public function bySchedule(Request $request)
    {
        $request->validate([
            'start_date' => 'required|date'
        ]);

        $query = Job::query()->where('start_date', $request->start_date);

        if ($request->has('with')) {
            $query->with($request->with);
        }

        return JobResource::collection($query->get());
    }
}
