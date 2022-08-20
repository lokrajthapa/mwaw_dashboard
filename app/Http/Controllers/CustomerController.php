<?php

namespace App\Http\Controllers;

use App\Http\Resources\CustomerResource;
use App\Models\Customer;
use Illuminate\Http\Request;

class CustomerController extends Controller
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
        $query = Customer::query()
            ->where(function ($q) use ($request) {
                $q->where('first_name', 'like', '%' . $request->search . '%')
                    ->orWhere('last_name', 'like', '%' . $request->search . '%');
            });

        if ($request->has('with')) {
            $query->with($request->with);
        }

        if ($request->has('filters')) {
            $query = getQueryWithFilters(json_decode($request->filters, true), $query);
        }

        $query->orderBy($request->sortBy ?: 'created_at', $request->sortDesc == 'true' ? 'desc' : 'asc');

        return CustomerResource::collection($query->paginate($request->itemsPerPage));

    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @return CustomerResource
     */
    public function store(Request $request)
    {
        $customer = Customer::create($request->all());
        return new CustomerResource($customer);
    }

    /**
     * Display the specified resource.
     *
     * @param \App\Models\Customer $customer
     * @return CustomerResource
     */
    public function show(Customer $customer)
    {
        return new CustomerResource($customer);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param \App\Models\Customer $customer
     * @return CustomerResource
     */
    public function update(Request $request, Customer $customer)
    {
        $customer->update($request->all());
        return new CustomerResource($customer);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param \App\Models\Customer $customer
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy(Customer $customer)
    {
        $customer->delete();
        return response()->json(['message' => 'success']);
    }
}
