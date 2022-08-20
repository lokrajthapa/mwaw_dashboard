<?php

namespace App\Http\Controllers;

use App\Http\Resources\SmsResource;
use App\Models\Sms;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class SmsController extends Controller
{
    public string $emergencyNumber;

    public function __construct()
    {
        $config = config('smstoemail');
        $this->emergencyNumber = $config['emergencyNumber'];
        $this->middleware(['auth:sanctum', 'hasAnyRole:admin'])->except(['store']);
        $this->middleware('fix.pagination')->only('index');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Resources\Json\AnonymousResourceCollection
     */
    public function index(Request $request)
    {
        $query = Sms::query()
            ->where(function ($q) use ($request) {
                $q->where('job_id', 'like', '%' . $request->search . '%')
                    ->orWhere('body', 'like', '%' . $request->search . '%');
            });


        if ($request->has('with')) {
            $query->with($request->with);
        }

        if ($request->has('filters')) {
            $query = getQueryWithFilters(json_decode($request->filters, true), $query);
        }

        $query->orderBy($request->sortBy ?: 'created_at', $request->sortDesc == 'true' ? 'desc' : 'asc');

        return SmsResource::collection($query->paginate($request->itemsPerPage));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @return SmsResource
     */
    public function store(Request $request)
    {
        $sms = new Sms([
            'from' => $request->From ?: $request->from,
            'to' => $request->To ?: $request->to,
            'body' => $request->Body ?: $request->body,
            'data' => $request->all(),
        ]);
        try {
            $parsedSms = $sms->parsedSms();
            $sms->job_id = $parsedSms['jobId']['value'] ?: null;
            $sms->type = $parsedSms['type'] ?: null;
        } catch (\Exception $e) {
        }

        $sms->save();
        $sms->refresh();
        try {
            if ($request->To == $this->emergencyNumber) {
//                Log::debug('notify numbers');
                $sms->sendEmergencyEmail();
                if (app()->environment('production')) {
                    if (!$request->has('test')) {
                        sendSms($request->To, $request->From, 'K');
                        $sms->notifyEmergencyNumbers();
                    }
                }
            } else {
                $sms->sendEmail();
                if (app()->environment('production')) {
                    if (!$request->has('test')) {
                        sendSms($request->To, $request->From, 'K');
                    }
                }
            }
        } catch (\Exception $e) {
            Log::error($e->getMessage(), [$sms->body]);
        }

        return new SmsResource($sms);
    }

    /**
     * Display the specified resource.
     *
     * @param \App\Models\Sms $sms
     * @return SmsResource
     */
    public function show(Sms $sms)
    {
        return new SmsResource($sms);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param \App\Models\Sms $sms
     * @return SmsResource
     */
    public function update(Request $request, Sms $sms)
    {
        $sms->update($request->all());
        $sms->refresh();
        return new SmsResource($sms);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param \App\Models\Sms $sms
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy(Sms $sms)
    {
        $sms->delete();
        return response()->json(['message' => 'success']);
    }

    public function resendEmail(Request $request, Sms $sms)
    {
        if ($sms->to == $this->emergencyNumber) {
            $sms->sendEmergencyEmail();
        } else {
            $sms->sendEmail();
        }
        return response()->json(['message' => 'success']);
    }
}
