<?php

namespace App\Http\Controllers;

use App\Http\Resources\FlexConferenceResource;
use App\Models\FlexConference;
use Illuminate\Http\Request;

class FlexConferenceController extends Controller
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
        $query = FlexConference::query()
            ->where(function ($q) use ($request) {
                $q->where('sid', 'like', '%' . $request->search . '%')
                    ->orWhereHas('calls', function ($q) use ($request) {
                        $q->where('from', 'like', '%' . $request->search . '%')
                            ->orWhere('to', 'like', '%' . $request->search . '%');
                    });
            });

        if ($request->has('with')) {
            $query->with($request->with);
        }

        if ($request->has('filters')) {
            $filters = json_decode($request->filters, true);

            if (key_exists('from', $filters) && $filters['from']) {
                $query->whereHas('calls', function ($q) use ($filters) {
                    $q->whereIn('from', $filters['from'])
                        ->orWhereIn('to', $filters['from']);
                });
                unset($filters['from']);
            }

            if (key_exists('to', $filters) && $filters['to']) {
                $query->whereHas('calls', function ($q) use ($filters) {
                    $q->whereIn('to', $filters['to'])
                        ->orWhereIn('from', $filters['to']);
                });
                unset($filters['to']);
            }

            $query = getQueryWithFilters($filters, $query);
        }

        $query->orderBy($request->sortBy ?: 'created_at', $request->sortDesc == 'true' ? 'desc' : 'asc');

        return FlexConferenceResource::collection($query->paginate($request->itemsPerPage));

    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @return FlexConferenceResource
     */
    public function store(Request $request)
    {
        $flexConference = FlexConference::create($request->all());
        return new FlexConferenceResource($flexConference);
    }

    /**
     * Display the specified resource.
     *
     * @param \App\Models\FlexConference $flexConference
     * @return FlexConferenceResource
     */
    public function show(FlexConference $flexConference)
    {
        return new FlexConferenceResource($flexConference);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param \App\Models\FlexConference $flexConference
     * @return FlexConferenceResource
     */
    public function update(Request $request, FlexConference $flexConference)
    {
        $flexConference->update($request->all());
        return new FlexConferenceResource($flexConference);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param \App\Models\FlexConference $flexConference
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy(FlexConference $flexConference)
    {
        $flexConference->delete();
        return response()->json(['message' => 'success']);
    }
}
