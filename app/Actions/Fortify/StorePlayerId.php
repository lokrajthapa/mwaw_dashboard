<?php

namespace App\Actions\Fortify;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;

class StorePlayerId
{

    /**
     * Handle the incoming request.
     *
     * @param \Illuminate\Http\Request $request
     * @param callable $next
     * @return mixed
     */
    public function handle($request, $next)
    {
        if ($request->has('playerId')) {
            error_log($request->playerId);
            $request->session()->put('playerId', $request->playerId);
        }
//        Log::debug('sessions', Session::all());
        return $next($request);
    }
}
