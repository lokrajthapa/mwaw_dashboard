<?php

namespace App\Http\Controllers;

use App\Http\Resources\SubCategoryResource;
use App\Models\SubCategory;
use Illuminate\Http\Request;

class SubCategoryController extends Controller
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
        $query = SubCategory::query()
            ->where('name', 'like', '%' . $request->search . '%');

        if ($request->has('with')) {
            $query->with($request->with);
        }

        if ($request->has('filters')) {
            $query = getQueryWithFilters(json_decode($request->filters, true), $query);
        }

        $query->orderBy($request->sortBy ?: 'created_at', $request->sortDesc == 'true' ? 'desc' : 'asc');

        return SubCategoryResource::collection($query->paginate($request->itemsPerPage));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @return SubCategoryResource
     */
    public function store(Request $request)
    {
        $subCategory = SubCategory::create($request->all());
        return new SubCategoryResource($subCategory);
    }

    /**
     * Display the specified resource.
     *
     * @param \App\Models\SubCategory $subCategory
     * @return \Illuminate\Http\Response
     */
    public function show(SubCategory $subCategory)
    {
        return new SubCategoryResource($subCategory);

    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param \App\Models\SubCategory $subCategory
     * @return SubCategoryResource
     */
    public function update(Request $request, SubCategory $subCategory)
    {
        $subCategory->update($request->all());
        return new SubCategoryResource($subCategory);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param \App\Models\SubCategory $subCategory
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy(SubCategory $subCategory)
    {
        $subCategory->delete();
        return response()->json(['message' => 'success']);
    }
}
