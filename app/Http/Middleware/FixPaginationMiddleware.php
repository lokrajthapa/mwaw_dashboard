<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class FixPaginationMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        if ($request->filled('itemsPerPage') && $request->itemsPerPage == '-1') {
            $request->merge(['itemsPerPage' => 1000]);
        }
        return $next($request);
    }
}
